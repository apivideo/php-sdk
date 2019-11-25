<?php


use ApiVideo\Client\Api\AnalyticsLive;
use Buzz\Message\Response;
use PHPUnit\Framework\TestCase;

class AnalyticsLiveTest extends TestCase
{
    /**
     * @test
     * @throws ReflectionException
     */
    public function getSucceed()
    {

        $analyticReturn = $this->getLiveAnalytic();

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($analyticReturn));


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $AnalyticsLive = new AnalyticsLive($oAuthBrowser);
        $analytic  = $AnalyticsLive->get('li55mglWKqgywdX8Yu8WgDZ0', '2018-07-31');

        $analyticExpected = json_decode($analyticReturn, true);
        $this->assertInstanceOf('ApiVideo\Client\Model\Analytic\PlayerSession', $analytic);
        $this->assertSame($analyticExpected['live']['liveStreamId'], $analytic->liveStreamId);
        $this->assertSame($analyticExpected['live']['name'], $analytic->name);
        $this->assertSame($analyticExpected['period'], $analytic->period);
        $this->assertNotEmpty($analytic->data);
        $this->assertCount(3, $analytic->data);

    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function getFailed()
    {
        $returned = '{
            "status": 400,
            "type": "https://docs.api.Live/problems/ressource.not_found",
            "title": "The requested resource was not found.",
            "name": "LiveId"
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 400);


        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($returned));


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $AnalyticsLive = new AnalyticsLive($oAuthBrowser);
        $analytic  = $AnalyticsLive->get('liWKqgywdX55mgl8Yu8WgDZ0');

        $this->assertNull($analytic);
        $error = $AnalyticsLive->getLastError();

        $this->assertSame(400, $error['status']);
        $this->assertSame(json_decode($returned, true), $error['message']);

    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function searchSucceed()
    {
        $returned = $this->getCollectionAnalyticsLive();
        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($returned));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $AnalyticsLive = new AnalyticsLive($oAuthBrowser);
        $results = $AnalyticsLive->search();

        $LivesReflected = new ReflectionClass('ApiVideo\Client\Api\Lives');
        $castAll         = $LivesReflected->getMethod('castAll');
        $castAll->setAccessible(true);

        $AnalyticsLiveReturn = json_decode($returned, true);
        unset($AnalyticsLiveReturn['period']);
        $this->assertEquals(array_merge(array(), $castAll->invokeArgs($AnalyticsLive, $AnalyticsLiveReturn)), $results);


    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function searchWithPaginationSucceed()
    {
        $returned = $this->getCollectionAnalyticsLive();
        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($returned));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $AnalyticsLive = new AnalyticsLive($oAuthBrowser);
        $results = $AnalyticsLive->search(array('currentPage' => 1));

        $LivesReflected = new ReflectionClass('ApiVideo\Client\Api\Lives');
        $castAll         = $LivesReflected->getMethod('castAll');
        $castAll->setAccessible(true);

        $AnalyticsLiveReturn = json_decode($returned, true);
        unset($AnalyticsLiveReturn['period']);
        $this->assertEquals(array_merge(array(), $castAll->invokeArgs($AnalyticsLive, $AnalyticsLiveReturn)), $results);


    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function searchWithBadParametersShouldFailed()
    {
        $return = '{
            "status": 400,
            "type": "https://docs.api.Live/problems/invalid.pagination",
            "title": "Invalid page. Must be at least equal to 1",
            "name": "page"
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 400);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($return));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $AnalyticsLive = new AnalyticsLive($oAuthBrowser);
        $results = $AnalyticsLive->search(
            array(
                'currentPage' => 0,
                'pageSize'    => 25,
            )
        );
        $this->assertNull($results);
        $error = $AnalyticsLive->getLastError();

        $this->assertSame(400, $error['status']);
        $return = json_decode($return, true);
        $this->assertSame($return, $error['message']);
    }

    private function getMockedOAuthBrowser()
    {
        return $this->getMockBuilder('ApiVideo\Client\Buzz\OAuthBrowser')
                    ->setMethods(array('get', 'submit', 'post', 'patch', 'delete'))
                    ->getMock();
    }

    private function getLiveAnalytic()
    {
        return '
        {
            "live": {
                "liveStreamId": "li55mglWKqgywdX8Yu8WgDZ0",
                "name": "Test"
            },
            "period": "2018-07-31",
            "data": [
                {
                    "session": {
                        "sessionId": "psJd8U77m2BddeNwM5A1jrG0",
                        "loadedAt": "2018-07-31 15:17:49.822+02",
                        "endedAt": "2018-07-31T15:17:49.822000+02:00"
                    },
                    "location": {
                        "country": "France",
                        "city": "Paris"
                    },
                    "referrer": {
                        "url": "unknown",
                        "medium": "unknown",
                        "source": "unknown",
                        "searchTerm": "unknown"
                    },
                    "device": {
                        "type": "desktop",
                        "vendor": "unknown",
                        "model": "unknown"
                    },
                    "os": {
                        "name": "unknown",
                        "shortname": "unknown",
                        "version": "unknown"
                    },
                    "client": {
                        "type": "browser",
                        "name": "Firefox",
                        "version": "61.0"
                    }
                },
                {
                    "session": {
                        "sessionId": "ps4CPJ1MTXUBAzZExi9JQXpx",
                        "loadedAt": "2018-07-31 15:17:49.822+02",
                        "endedAt": "2018-07-31T15:17:49.822000+02:00"
                    },
                    "location": {
                        "country": "France",
                        "city": "Paris"
                    },
                    "referrer": {
                        "url": "unknown",
                        "medium": "unknown",
                        "source": "unknown",
                        "searchTerm": "unknown"
                    },
                    "device": {
                        "type": "desktop",
                        "vendor": "unknown",
                        "model": "unknown"
                    },
                    "os": {
                        "name": "unknown",
                        "shortname": "unknown",
                        "version": "unknown"
                    },
                    "client": {
                        "type": "browser",
                        "name": "Firefox",
                        "version": "61.0"
                    }
                },
                {
                    "session": {
                        "sessionId": "psp02UdkjoXu5JzO4mc6sOj",
                        "loadedAt": "2018-07-31 15:17:49.822+02",
                        "endedAt": "2018-07-31T15:17:49.822000+02:00"
                    },
                    "location": {
                        "country": "France",
                        "city": "Paris"
                    },
                    "referrer": {
                        "url": "unknown",
                        "medium": "unknown",
                        "source": "unknown",
                        "searchTerm": "unknown"
                    },
                    "device": {
                        "type": "desktop",
                        "vendor": "unknown",
                        "model": "unknown"
                    },
                    "os": {
                        "name": "unknown",
                        "shortname": "unknown",
                        "version": "unknown"
                    },
                    "client": {
                        "type": "browser",
                        "name": "Firefox",
                        "version": "61.0"
                    }
                }
            ]
        }';
    }

    private function getCollectionAnalyticsLive()
    {
        return '{
            "period": "2018-08-02",
            "data": [
            {
                "live": {
                    "liveStreamId": "li55mglWKqgywdX8Yu8WgDZ0",
                    "name": "Test"
                },
                "period": "2018-07-31",
                "data": [
                    {
                        "session": {
                            "sessionId": "psJd8U77m2BddeNwM5A1jrG0",
                            "loadedAt": "2018-07-31 15:17:49.822+02",
                            "endedAt": "2018-07-31T15:17:49.822000+02:00"
                        },
                        "location": {
                            "country": "France",
                            "city": "Paris"
                        },
                        "referrer": {
                            "url": "unknown",
                            "medium": "unknown",
                            "source": "unknown",
                            "searchTerm": "unknown"
                        },
                        "device": {
                            "type": "desktop",
                            "vendor": "unknown",
                            "model": "unknown"
                        },
                        "os": {
                            "name": "unknown",
                            "shortname": "unknown",
                            "version": "unknown"
                        },
                        "client": {
                            "type": "browser",
                            "name": "Firefox",
                            "version": "61.0"
                        }
                    },
                    {
                        "session": {
                            "sessionId": "ps4CPJ1MTXUBAzZExi9JQXpx",
                            "loadedAt": "2018-07-31 15:17:49.822+02",
                            "endedAt": "2018-07-31T15:17:49.822000+02:00"
                        },
                        "location": {
                            "country": "France",
                            "city": "Paris"
                        },
                        "referrer": {
                            "url": "unknown",
                            "medium": "unknown",
                            "source": "unknown",
                            "searchTerm": "unknown"
                        },
                        "device": {
                            "type": "desktop",
                            "vendor": "unknown",
                            "model": "unknown"
                        },
                        "os": {
                            "name": "unknown",
                            "shortname": "unknown",
                            "version": "unknown"
                        },
                        "client": {
                            "type": "browser",
                            "name": "Firefox",
                            "version": "61.0"
                        }
                    },
                    {
                        "session": {
                            "sessionId": "psp02UdkjoXu5JzO4mc6sOj",
                            "loadedAt": "2018-07-31 15:17:49.822+02",
                            "endedAt": "2018-07-31T15:17:49.822000+02:00"
                        },
                        "location": {
                            "country": "France",
                            "city": "Paris"
                        },
                        "referrer": {
                            "url": "unknown",
                            "medium": "unknown",
                            "source": "unknown",
                            "searchTerm": "unknown"
                        },
                        "device": {
                            "type": "desktop",
                            "vendor": "unknown",
                            "model": "unknown"
                        },
                        "os": {
                            "name": "unknown",
                            "shortname": "unknown",
                            "version": "unknown"
                        },
                        "client": {
                            "type": "browser",
                            "name": "Firefox",
                            "version": "61.0"
                        }
                    }
                ]
            }],
            "pagination": {
                "currentPage": 1,
                "pageSize": 25,
                "pagesTotal": 1,
                "itemsTotal": 1,
                "currentPageItems": 1,
                "links": [
                    {
                        "rel": "self",
                        "uri": "http://ws.api.Live/analytics/live-streams?currentPage=1"
                    },
                    {
                        "rel": "first",
                        "uri": "http://ws.api.Live/analytics/live-streams?currentPage=1"
                    },
                    {
                        "rel": "last",
                        "uri": "http://ws.api.Live/analytics/live-streams?currentPage=1"
                    }
                ]
            }
        }';
    }
}
