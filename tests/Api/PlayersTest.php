<?php

namespace ApiVideo\Client\Tests\Api;

use ApiVideo\Client\Api\Players;
use ApiVideo\Client\Model\Player;
use Buzz\Message\Response;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PlayersTest extends TestCase
{
    /**
     * @test
     * @throws \ReflectionException
     */
    public function getSucceed()
    {
        $playerId     = 'pl55mglWKqgywdX8Yu8WgDZ0';
        $playerReturn = '
        {
            "playerId": "pl55mglWKqgywdX8Yu8WgDZ0",
            "shapeMargin": 10,
            "shapeRadius": 3,
            "shapeAspect": "flat",
            "shapeBackgroundTop": "rgba(50, 50, 50, .7)",
            "shapeBackgroundBottom": "rgba(50, 50, 50, .8)",
            "text": "rgba(255, 255, 255, .95)",
            "link": "rgba(255, 0, 0, .95)",
            "linkHover": "rgba(255, 255, 255, .75)",
            "linkActive": "rgba(255, 0, 0, .75)",
            "trackPlayed": "rgba(255, 255, 255, .95)",
            "trackUnplayed": "rgba(255, 255, 255, .1)",
            "trackBackground": "rgba(0, 0, 0, 0)",
            "backgroundTop": "rgba(72, 4, 45, 1)",
            "backgroundBottom": "rgba(94, 95, 89, 1)",
            "backgroundText": "rgba(255, 255, 255, .95)",
            "enableApi": true,
            "enableControls": true,
            "forceAutoplay": false,
            "hideTitle": false,
            "forceLoop": false
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($playerReturn));

        $playerArray = json_decode($playerReturn, true);

        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $players = new Players($oAuthBrowser);
        $player  = $players->get($playerId);

        $this->assertInstanceOf('ApiVideo\Client\Model\Player', $player);
        $this->assertSame('pl55mglWKqgywdX8Yu8WgDZ0', $player->playerId);
        $this->assertSame($playerArray['shapeMargin'], $player->shapeMargin);
        $this->assertSame($playerArray['shapeMargin'], $player->shapeMargin);
        $this->assertSame($playerArray['shapeAspect'], $player->shapeAspect);
        $this->assertSame($playerArray['shapeBackgroundTop'], $player->shapeBackgroundTop);
        $this->assertSame($playerArray['shapeBackgroundBottom'], $player->shapeBackgroundBottom);
        $this->assertSame($playerArray['text'], $player->text);
        $this->assertSame($playerArray['link'], $player->link);
        $this->assertSame($playerArray['linkHover'], $player->linkHover);
        $this->assertSame($playerArray['linkActive'], $player->linkActive);
        $this->assertSame($playerArray['trackPlayed'], $player->trackPlayed);
        $this->assertSame($playerArray['trackUnplayed'], $player->trackUnplayed);
        $this->assertSame($playerArray['trackBackground'], $player->trackBackground);
        $this->assertSame($playerArray['backgroundTop'], $player->backgroundTop);
        $this->assertSame($playerArray['backgroundBottom'], $player->backgroundBottom);
        $this->assertSame($playerArray['backgroundText'], $player->backgroundText);
        $this->assertSame($playerArray['enableApi'], $player->enableApi);
        $this->assertSame($playerArray['enableControls'], $player->enableControls);
        $this->assertSame($playerArray['forceAutoplay'], $player->forceAutoplay);
        $this->assertSame($playerArray['hideTitle'], $player->hideTitle);
        $this->assertSame($playerArray['forceLoop'], $player->forceLoop);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getFailed()
    {
        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 404);


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $players = new Players($oAuthBrowser);
        $player  = $players->get('pllWKqgy55mgwdX8Yu8WgDZ0');

        $this->assertNull($player);
        $error = $players->getLastError();

        $this->assertSame(404, $error['status']);
        $this->assertEmpty($error['message']);

    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function searchSucceed()
    {
        $playerReturn = '
        {
            "data": [
                {
                    "playerId": "pl1pv6rbSu0GetgVUzFzTTkd",
                    "shapeMargin": 10,
                    "shapeRadius": 3,
                    "shapeAspect": "flat",
                    "shapeBackgroundTop": "rgba(50, 50, 50, .7)",
                    "shapeBackgroundBottom": "rgba(50, 50, 50, .8)",
                    "text": "rgba(255, 255, 255, .95)",
                    "link": "rgba(255, 0, 0, .95)",
                    "linkHover": "rgba(255, 255, 255, .75)",
                    "linkActive": "rgba(255, 0, 0, .75)",
                    "trackPlayed": "rgba(255, 255, 255, .95)",
                    "trackUnplayed": "rgba(255, 255, 255, .1)",
                    "trackBackground": "rgba(0, 0, 0, 0)",
                    "backgroundTop": "rgba(72, 4, 45, 1)",
                    "backgroundBottom": "rgba(94, 95, 89, 1)",
                    "backgroundText": "rgba(255, 255, 255, .95)",
                    "enableApi": true,
                    "enableControls": true,
                    "forceAutoplay": false,
                    "hideTitle": false,
                    "forceLoop": false
                }
            ],
            "pagination": {
                "currentPage": 1,
                "pageSize": 25,
                "pagesTotal": 1,
                "itemsTotal": 1,
                "currentPageItems": 1,
                "links": [
                    {
                        "rel": "self",
                        "uri": "https://ws-staging.api.player/players?currentPage=1"
                    },
                    {
                        "rel": "first",
                        "uri": "https://ws-staging.api.player/players?currentPage=1"
                    },
                    {
                        "rel": "last",
                        "uri": "https://ws-staging.api.player/players?currentPage=1"
                    }
                ]
            }
        }';

        $response          = new Response();
        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($playerReturn));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $players = new Players($oAuthBrowser);
        $results = $players->search();

        $playersReflected = new ReflectionClass('ApiVideo\Client\Api\Players');
        $castAll          = $playersReflected->getMethod('castAll');
        $castAll->setAccessible(true);

        $playersReturn = json_decode($playerReturn, true);

        $this->assertEquals(array_merge(array(), $castAll->invokeArgs($players, $playersReturn)), $results);


    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function searchWithBadCurrentPagePaginationShouldFailed()
    {
        $return = '{
            "status": 400,
            "type": "https://docs.api.video/problems/invalid.pagination",
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

        $players = new Players($oAuthBrowser);
        $results = $players->search(
            array(
                'currentPage' => 0,
                'pageSize'    => 25,
            )
        );
        $this->assertNull($results);
        $error = $players->getLastError();

        $this->assertSame(400, $error['status']);
        $return = json_decode($return, true);
        $this->assertSame($return, $error['message']);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function searchWithCallback()
    {
        $playerReturn = '
        {
            "data": [
                {
                    "playerId": "pl1pv6rbSu0GetgVUzFzTTkd",
                    "shapeMargin": 10,
                    "shapeRadius": 3,
                    "shapeAspect": "flat",
                    "shapeBackgroundTop": "rgba(50, 50, 50, .7)",
                    "shapeBackgroundBottom": "rgba(50, 50, 50, .8)",
                    "text": "rgba(255, 255, 255, .95)",
                    "link": "rgba(255, 0, 0, .95)",
                    "linkHover": "rgba(255, 255, 255, .75)",
                    "linkActive": "rgba(255, 0, 0, .75)",
                    "trackPlayed": "rgba(255, 255, 255, .95)",
                    "trackUnplayed": "rgba(255, 255, 255, .1)",
                    "trackBackground": "rgba(0, 0, 0, 0)",
                    "backgroundTop": "rgba(72, 4, 45, 1)",
                    "backgroundBottom": "rgba(94, 95, 89, 1)",
                    "backgroundText": "rgba(255, 255, 255, .95)",
                    "enableApi": true,
                    "enableControls": true,
                    "forceAutoplay": false,
                    "hideTitle": false,
                    "forceLoop": false
                }
            ],
            "pagination": {
                "currentPage": 1,
                "pageSize": 25,
                "pagesTotal": 1,
                "itemsTotal": 1,
                "currentPageItems": 1,
                "links": [
                    {
                        "rel": "self",
                        "uri": "/players?pageSize=25"
                    },
                    {
                        "rel": "first",
                        "uri": "/players?pageSize=25"
                    },
                    {
                        "rel": "last",
                        "uri": "/players?pageSize=25"
                    }
                ]
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($playerReturn));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $players  = new Players($oAuthBrowser);

        $class = $this;
        $callback = function($player) use ($class){
            $class->assertNotNull($player->playerId);
        };

        $players->search(
            array(
                'currentPage' => 1,
                'pageSize'    => 25,
            ),
            $callback
        );

    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function searchWithBadPaginationShouldReturnEmptyData()
    {
        $playerReturn = '
        {
            "data": [],
            "pagination": {
                "currentPage": 12000,
                "pageSize": 25,
                "pagesTotal": 1,
                "itemsTotal": 3,
                "currentPageItems": -299972,
                "links": [
                    {
                        "rel": "self",
                        "uri": "https://localhost/players?currentPage=1"
                    },
                    {
                        "rel": "first",
                        "uri": "https://localhost/players?currentPage=1"
                    },
                    {
                        "rel": "last",
                        "uri": "https://localhost/players?currentPage=1"
                    }
                ]
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($playerReturn));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $players = new Players($oAuthBrowser);
        $results = $players->search(
            array(
                'currentPage' => 1,
                'pageSize'    => 25,
                'sortBy'      => 'title',
                'sortOrder'   => 'asc',
            )
        );

        $playersReflected = new ReflectionClass('ApiVideo\Client\Api\Players');
        $castAll          = $playersReflected->getMethod('castAll');
        $castAll->setAccessible(true);

        $playersReturn = json_decode($playerReturn, true);

        $this->assertEquals(array_merge(array(), $castAll->invokeArgs($players, $playersReturn)), $results);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function createSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $playerReturn = '
        {
            "playerId": "pl55mglWKqgywdX8Yu8WgDZ0",
            "shapeMargin": 10,
            "shapeRadius": 3,
            "shapeAspect": "flat",
            "shapeBackgroundTop": "rgba(50, 50, 50, .7)",
            "shapeBackgroundBottom": "rgba(50, 50, 50, .8)",
            "text": "rgba(255, 255, 255, .95)",
            "link": "rgba(255, 0, 0, .95)",
            "linkHover": "rgba(255, 255, 255, .75)",
            "linkActive": "rgba(255, 0, 0, .75)",
            "trackPlayed": "rgba(255, 255, 255, .95)",
            "trackUnplayed": "rgba(255, 255, 255, .1)",
            "trackBackground": "rgba(0, 0, 0, 0)",
            "backgroundTop": "rgba(72, 4, 45, 1)",
            "backgroundBottom": "rgba(94, 95, 89, 1)",
            "backgroundText": "rgba(255, 255, 255, .95)",
            "enableApi": true,
            "enableControls": true,
            "forceAutoplay": false,
            "hideTitle": false,
            "forceLoop": false
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($playerReturn));

        $playerArray = json_decode($playerReturn, true);
        $mockedBrowser->method('post')->willReturn($response);

        $players = new Players($mockedBrowser);
        /** @var Player $result */
        $player = $players->create();
        $this->assertInstanceOf('ApiVideo\Client\Model\Player', $player);
        $this->assertSame('pl55mglWKqgywdX8Yu8WgDZ0', $player->playerId);
        $this->assertSame($playerArray['shapeMargin'], $player->shapeMargin);
        $this->assertSame($playerArray['shapeMargin'], $player->shapeMargin);
        $this->assertSame($playerArray['shapeAspect'], $player->shapeAspect);
        $this->assertSame($playerArray['shapeBackgroundTop'], $player->shapeBackgroundTop);
        $this->assertSame($playerArray['shapeBackgroundBottom'], $player->shapeBackgroundBottom);
        $this->assertSame($playerArray['text'], $player->text);
        $this->assertSame($playerArray['link'], $player->link);
        $this->assertSame($playerArray['linkHover'], $player->linkHover);
        $this->assertSame($playerArray['linkActive'], $player->linkActive);
        $this->assertSame($playerArray['trackPlayed'], $player->trackPlayed);
        $this->assertSame($playerArray['trackUnplayed'], $player->trackUnplayed);
        $this->assertSame($playerArray['trackBackground'], $player->trackBackground);
        $this->assertSame($playerArray['backgroundTop'], $player->backgroundTop);
        $this->assertSame($playerArray['backgroundBottom'], $player->backgroundBottom);
        $this->assertSame($playerArray['backgroundText'], $player->backgroundText);
        $this->assertSame($playerArray['enableApi'], $player->enableApi);
        $this->assertSame($playerArray['enableControls'], $player->enableControls);
        $this->assertSame($playerArray['forceAutoplay'], $player->forceAutoplay);
        $this->assertSame($playerArray['hideTitle'], $player->hideTitle);
        $this->assertSame($playerArray['forceLoop'], $player->forceLoop);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function createWithEmptyBodyShouldFailed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $return = '{
            "status": 400,"type": "https://docs.api.video/problems/payload.missing",
            "title": "Request payload is missing."
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 400);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($return));

        $mockedBrowser->method('post')->willReturn($response);

        $players = new Players($mockedBrowser);
        $result = $players->create(array());

        $this->assertNull($result);
        $error = $players->getLastError();

        $this->assertSame(400, $error['status']);
        $return = json_decode($return, true);
        $this->assertSame($return, $error['message']);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function createWithPropertiesSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $playerReturn = '
        {
            "playerId": "pl55mglWKqgywdX8Yu8WgDZ0",
            "shapeMargin": 10,
            "shapeRadius": 3,
            "shapeAspect": "flat",
            "shapeBackgroundTop": "rgba(50, 50, 50, .7)",
            "shapeBackgroundBottom": "rgba(50, 50, 50, .8)",
            "text": "rgba(255, 255, 255, .95)",
            "link": "rgba(255, 0, 0, .95)",
            "linkHover": "rgba(255, 255, 255, .75)",
            "linkActive": "rgba(255, 0, 0, .75)",
            "trackPlayed": "rgba(255, 255, 255, .95)",
            "trackUnplayed": "rgba(255, 255, 255, .1)",
            "trackBackground": "rgba(0, 0, 0, 0)",
            "backgroundTop": "rgba(72, 4, 45, 1)",
            "backgroundBottom": "rgba(94, 95, 89, 1)",
            "backgroundText": "rgba(255, 255, 255, .95)",
            "enableApi": true,
            "enableControls": true,
            "forceAutoplay": true,
            "hideTitle": false,
            "forceLoop": false
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($playerReturn));

        $mockedBrowser->method('post')->willReturn($response);

        $players = new Players($mockedBrowser);
        /** @var Player $result */
        $player = $players->create(array('forceAutoplay' => true));
        $this->assertTrue($player->forceAutoplay);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function updateSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $playerReturn = '
        {
            "playerId": "pl55mglWKqgywdX8Yu8WgDZ0",
            "shapeMargin": 10,
            "shapeRadius": 3,
            "shapeAspect": "flat",
            "shapeBackgroundTop": "rgba(50, 50, 50, .7)",
            "shapeBackgroundBottom": "rgba(50, 50, 50, .8)",
            "text": "rgba(255, 255, 255, .95)",
            "link": "rgba(255, 0, 0, .95)",
            "linkHover": "rgba(255, 255, 255, .75)",
            "linkActive": "rgba(255, 0, 0, .75)",
            "trackPlayed": "rgba(255, 255, 255, .95)",
            "trackUnplayed": "rgba(255, 255, 255, .1)",
            "trackBackground": "rgba(0, 0, 0, 0)",
            "backgroundTop": "rgba(72, 4, 45, 1)",
            "backgroundBottom": "rgba(94, 95, 89, 1)",
            "backgroundText": "rgba(255, 255, 255, .95)",
            "enableApi": true,
            "enableControls": true,
            "forceAutoplay": false,
            "hideTitle": false,
            "forceLoop": false
        }';

        $playerPatch = '
        {
            "playerId": "pl55mglWKqgywdX8Yu8WgDZ0",
            "shapeMargin": 10,
            "shapeRadius": 3,
            "shapeAspect": "flat",
            "shapeBackgroundTop": "rgba(50, 50, 50, .7)",
            "shapeBackgroundBottom": "rgba(50, 50, 50, .8)",
            "text": "rgba(255, 255, 255, .95)",
            "link": "rgba(255, 0, 0, .95)",
            "linkHover": "rgba(255, 255, 255, .75)",
            "linkActive": "rgba(255, 0, 0, .75)",
            "trackPlayed": "rgba(255, 255, 255, .95)",
            "trackUnplayed": "rgba(255, 255, 255, .1)",
            "trackBackground": "rgba(0, 0, 0, 0)",
            "backgroundTop": "rgba(72, 4, 45, 1)",
            "backgroundBottom": "rgba(94, 95, 89, 1)",
            "backgroundText": "rgba(255, 255, 255, .95)",
            "enableApi": true,
            "enableControls": true,
            "forceAutoplay": true,
            "hideTitle": true,
            "forceLoop": true
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($playerReturn));


        $responsePatch = new Response();

        $responsePatchReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCodePatch        = $responsePatchReflected->getProperty('statusCode');
        $statusCodePatch->setAccessible(true);
        $statusCodePatch->setValue($responsePatch, 200);
        $setContentPatch = $responsePatchReflected->getMethod('setContent');
        $setContentPatch->invokeArgs($responsePatch, array($playerPatch));
        $responsePatch->setContent($playerPatch);

        $player           = json_decode($playerReturn, true);
        $playerProperties = json_decode($playerReturn, true);

        $mockedBrowser->method('post')->willReturn($response);
        $mockedBrowser->method('patch')->willReturn($responsePatch);

        $players = new Players($mockedBrowser);
        /** @var Player $result */
        $result = $players->create($player);
        $this->assertFalse($result->forceAutoplay);
        $this->assertFalse($result->hideTitle);
        $resultPath = $players->update($result->playerId, $playerProperties);
        $this->assertTrue($resultPath->forceAutoplay);
        $this->assertTrue($resultPath->hideTitle);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function updateWihEmptyBodyFailed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $return = '{
            "status": 400,"type": "https://docs.api.video/problems/payload.missing",
            "title": "Request payload is missing."
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 400);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($return));

        $mockedBrowser->method('patch')->willReturn($response);

        $players = new Players($mockedBrowser);
        $result = $players->update('pl55mglWKqgywdX8Yu8WgDZ0', array());

        $this->assertNull($result);
        $error = $players->getLastError();

        $this->assertSame(400, $error['status']);
        $return = json_decode($return, true);
        $this->assertSame($return, $error['message']);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function deleteSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 204);

        $mockedBrowser->method('delete')->willReturn($response);

        $players = new Players($mockedBrowser);

        $status = $players->delete('pl55mglWKqgywdX8Yu8WgDZ0');
        $this->assertSame(204, $status);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function deleteWithBadPlayerIdFailed()
    {
        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 404);

        $mockedBrowser = $this->getMockedOAuthBrowser();
        $mockedBrowser->method('delete')->willReturn($response);

        $players = new Players($mockedBrowser);
        $result = $players->delete('pl5lWKqg5mgywdX8Yu8WgDZ0');

        $this->assertNull($result);
        $error = $players->getLastError();

        $this->assertSame(404, $error['status']);
    }

    private function getMockedOAuthBrowser()
    {
        return $this->getMockBuilder('ApiVideo\Client\Buzz\OAuthBrowser')
                    ->setMethods(array('get', 'submit', 'post', 'patch', 'delete'))
                    ->getMock();
    }
}
