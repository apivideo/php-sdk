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
    public function searchSuccess()
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
        $collection  = $AnalyticsLive->search('li55mglWKqgywdX8Yu8WgDZ0', '2018-07-31');

        $this->assertInstanceOf('ApiVideo\Client\Model\Analytic\PlayerSession', $collection[0]);
        $this->assertNotEmpty($collection);
        $this->assertCount(3, $collection);

    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function searchFailure()
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
        $analytic  = $AnalyticsLive->search('liWKqgywdX55mgl8Yu8WgDZ0');

        $this->assertNull($analytic);
        $error = $AnalyticsLive->getLastError();

        $this->assertSame(400, $error['status']);
        $this->assertSame(json_decode($returned, true), $error['message']);

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
