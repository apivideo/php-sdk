<?php


use ApiVideo\Client\Model\Player;
use Buzz\Message\Response;
use ApiVideo\Client\Api\Players;
use PHPUnit\Framework\TestCase;

class PlayersTest extends TestCase
{
    /**
     * @test
     */
    public function getSucceed()
    {
        $playerId     = 'pl55mglWKqgywdX8Yu8WgDZ0';
        $playerReturn = '
        {
            "playerId": "pl55mglWKqgywdX8Yu8WgDZ0",
            "buttonTextInactive":"rgba(255, 255, 255, 1)",
            "buttonTextActive":"rgba(255, 255, 255, 1)",
            "buttonTextHover":"rgba(255, 255, 255, 1)",
            "buttonBackgroundTop":"rgba(255, 255, 255, 1)",
            "buttonBackgroundBottom":"rgba(255, 255, 255, 1)",
            "buttonRadius":20,
            "buttonLightEffect":true,
            "controlMargin":"rgba(255, 255, 255, 1)",
            "controlLogo":null,
            "controlLogoUrl":null,
            "trackbarPlayedTop":"rgba(255, 255, 255, 1)",
            "trackbarPlayedBottom":"rgba(255, 255, 255, 1)",
            "trackbarBackgroundTop":"rgba(255, 255, 255, 1)",
            "trackbarBackgroundBottom":"rgba(255, 255, 255, 1)",
            "trackbarTextColor":"rgba(255, 255, 255, 1)",
            "panelTextInactive":"rgba(255, 255, 255, 1)",
            "panelTextActive":"rgba(255, 255, 255, 1)",
            "panelTextHover":"rgba(255, 255, 255, 1)",
            "panelBackgroundTop":"rgba(255, 255, 255, 1)",
            "panelBackgroundBottom":"rgba(255, 255, 255, 1)",
            "scrollbarThumb":"rgba(255, 255, 255, 1)",
            "scrollbarTrack":"rgba(255, 255, 255, 1)",
            "enableApi":false,"enableControls":true,
            "enableInfoPanel":true,
            "enableSharePanel":true,
            "enableDownloadPanel":true,
            "enableSettingsPanel":true,
            "forceAutoplay":false,
            "hideTitle":false
        }';

        $response = new Response();
        $response->setContent($playerReturn);

        $playerArray = json_decode($playerReturn, true);

        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $players = new Players($oAuthBrowser);
        $player  = $players->get($playerId);

        $this->assertInstanceOf('ApiVideo\Client\Model\Player', $player);
        $this->assertSame('pl55mglWKqgywdX8Yu8WgDZ0', $player->playerId);
        $this->assertSame($playerArray['enableApi'], $player->enableApi);
        $this->assertSame($playerArray['enableControls'], $player->enableControls);
        $this->assertSame($playerArray['enableInfoPanel'], $player->enableInfoPanel);
        $this->assertSame($playerArray['enableSharePanel'], $player->enableSharePanel);
        $this->assertSame($playerArray['enableDownloadPanel'], $player->enableDownloadPanel);
        $this->assertSame($playerArray['enableSettingsPanel'], $player->enableSettingsPanel);
        $this->assertSame($playerArray['forceAutoplay'], $player->forceAutoplay);
        $this->assertSame($playerArray['hideTitle'], $player->hideTitle);
        $this->assertSame($playerArray['buttonTextInactive'], $player->buttonTextInactive);
        $this->assertSame($playerArray['buttonTextActive'], $player->buttonTextActive);
        $this->assertSame($playerArray['buttonTextHover'], $player->buttonTextHover);
        $this->assertSame($playerArray['buttonBackgroundTop'], $player->buttonBackgroundTop);
        $this->assertSame($playerArray['buttonBackgroundBottom'], $player->buttonBackgroundBottom);
        $this->assertSame($playerArray['buttonRadius'], $player->buttonRadius);
        $this->assertSame($playerArray['buttonLightEffect'], $player->buttonLightEffect);
        $this->assertSame($playerArray['controlMargin'], $player->controlMargin);
        $this->assertSame($playerArray['controlLogo'], $player->controlLogo);
        $this->assertSame($playerArray['controlLogoUrl'], $player->controlLogoUrl);
        $this->assertSame($playerArray['trackbarPlayedTop'], $player->trackbarPlayedTop);
        $this->assertSame($playerArray['trackbarPlayedBottom'], $player->trackbarPlayedBottom);
        $this->assertSame($playerArray['trackbarBackgroundTop'], $player->trackbarBackgroundTop);
        $this->assertSame($playerArray['trackbarBackgroundBottom'], $player->trackbarBackgroundBottom);
        $this->assertSame($playerArray['trackbarTextColor'], $player->trackbarTextColor);
        $this->assertSame($playerArray['panelTextInactive'], $player->panelTextInactive);
        $this->assertSame($playerArray['panelTextActive'], $player->panelTextActive);
        $this->assertSame($playerArray['panelTextHover'], $player->panelTextHover);
        $this->assertSame($playerArray['panelBackgroundTop'], $player->panelBackgroundTop);
        $this->assertSame($playerArray['panelBackgroundBottom'], $player->panelBackgroundBottom);
        $this->assertSame($playerArray['scrollbarThumb'], $player->scrollbarThumb);
        $this->assertSame($playerArray['scrollbarTrack'], $player->scrollbarTrack);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function searchSucceed()
    {
        $playerReturn = '
        {
            "data": [
                {
                    "playerId": "pl1pv6rbSu0GetgVUzFzTTkd",
                    "enableApi": false,
                    "hideTitle": true,
                    "controlLogo": null,
                    "buttonRadius": 20,
                    "controlMargin": "rgba(255, 255, 255, 1)",
                    "forceAutoplay": true,
                    "controlLogoUrl": null,
                    "enableControls": false,
                    "panelTextHover": "rgba(255, 255, 255, 1)",
                    "scrollbarThumb": "rgba(255, 255, 255, 1)",
                    "scrollbarTrack": "rgba(255, 255, 255, 1)",
                    "buttonTextHover": "rgba(255, 255, 255, 1)",
                    "enableInfoPanel": true,
                    "panelTextActive": "rgba(255, 255, 255, 1)",
                    "buttonTextActive": "rgba(255, 255, 255, 1)",
                    "enableSharePanel": true,
                    "buttonLightEffect": true,
                    "panelTextInactive": "rgba(255, 255, 255, 1)",
                    "trackbarPlayedTop": "rgba(255, 255, 255, 1)",
                    "trackbarTextColor": "rgba(255, 255, 255, 1)",
                    "buttonTextInactive": "rgba(255, 255, 255, 1)",
                    "panelBackgroundTop": "rgba(255, 255, 255, 1)",
                    "buttonBackgroundTop": "rgba(255, 255, 255, 1)",
                    "enableDownloadPanel": true,
                    "enableSettingsPanel": true,
                    "trackbarPlayedBottom": "rgba(255, 255, 255, 1)",
                    "panelBackgroundBottom": "rgba(255, 255, 255, 1)",
                    "trackbarBackgroundTop": "rgba(255, 255, 255, 1)",
                    "buttonBackgroundBottom": "rgba(255, 255, 255, 1)",
                    "trackbarBackgroundBottom": "rgba(255, 255, 255, 1)"
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

        $response = new Response();
        $response->setContent($playerReturn);

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $players  = new Players($oAuthBrowser);
        $results = $players->search(
            array(
                'currentPage' => 1,
                'pageSize'    => 25,
            )
        );

        $playersReflected = new ReflectionClass('ApiVideo\Client\Api\Players');
        $castAll         = $playersReflected->getMethod('castAll');
        $castAll->setAccessible(true);

        $playersReturn = json_decode($playerReturn, true);

        $this->assertEquals(array_merge(array(), $castAll->invokeArgs($players, $playersReturn)), $results);


    }

    /**
     * @test
     * @throws ReflectionException
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
        $response->setContent($playerReturn);

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $players  = new Players($oAuthBrowser);
        $results = $players->search(
            array(
                'currentPage' => 1,
                'pageSize'    => 25,
                'sortBy'      => 'title',
                'sortOrder'   => 'asc',
            )
        );

        $playersReflected = new ReflectionClass('ApiVideo\Client\Api\Players');
        $castAll         = $playersReflected->getMethod('castAll');
        $castAll->setAccessible(true);

        $playersReturn = json_decode($playerReturn, true);

        $this->assertEquals(array_merge(array(), $castAll->invokeArgs($players, $playersReturn)), $results);
    }

    /**
     * @test
     */
    public function createSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $playerReturn = '
        {
            "playerId": "pl55mglWKqgywdX8Yu8WgDZ0",
            "buttonTextInactive":"rgba(255, 255, 255, 1)",
            "buttonTextActive":"rgba(255, 255, 255, 1)",
            "buttonTextHover":"rgba(255, 255, 255, 1)",
            "buttonBackgroundTop":"rgba(255, 255, 255, 1)",
            "buttonBackgroundBottom":"rgba(255, 255, 255, 1)",
            "buttonRadius":20,
            "buttonLightEffect":true,
            "controlMargin":"rgba(255, 255, 255, 1)",
            "controlLogo":null,
            "controlLogoUrl":null,
            "trackbarPlayedTop":"rgba(255, 255, 255, 1)",
            "trackbarPlayedBottom":"rgba(255, 255, 255, 1)",
            "trackbarBackgroundTop":"rgba(255, 255, 255, 1)",
            "trackbarBackgroundBottom":"rgba(255, 255, 255, 1)",
            "trackbarTextColor":"rgba(255, 255, 255, 1)",
            "panelTextInactive":"rgba(255, 255, 255, 1)",
            "panelTextActive":"rgba(255, 255, 255, 1)",
            "panelTextHover":"rgba(255, 255, 255, 1)",
            "panelBackgroundTop":"rgba(255, 255, 255, 1)",
            "panelBackgroundBottom":"rgba(255, 255, 255, 1)",
            "scrollbarThumb":"rgba(255, 255, 255, 1)",
            "scrollbarTrack":"rgba(255, 255, 255, 1)",
            "enableApi":false,"enableControls":true,
            "enableInfoPanel":true,
            "enableSharePanel":true,
            "enableDownloadPanel":true,
            "enableSettingsPanel":true,
            "forceAutoplay":false,
            "hideTitle":false
        }';

        $response = new Response();
        $response->setContent($playerReturn);

        $playerArray = json_decode($playerReturn, true);
        $mockedBrowser->method('post')->willReturn($response);

        $players = new Players($mockedBrowser);
        /** @var Player $result */
        $player = $players->create();
        $this->assertInstanceOf('ApiVideo\Client\Model\Player', $player);
        $this->assertSame('pl55mglWKqgywdX8Yu8WgDZ0', $player->playerId);
        $this->assertSame($playerArray['enableApi'], $player->enableApi);
        $this->assertSame($playerArray['enableControls'], $player->enableControls);
        $this->assertSame($playerArray['enableInfoPanel'], $player->enableInfoPanel);
        $this->assertSame($playerArray['enableSharePanel'], $player->enableSharePanel);
        $this->assertSame($playerArray['enableDownloadPanel'], $player->enableDownloadPanel);
        $this->assertSame($playerArray['enableSettingsPanel'], $player->enableSettingsPanel);
        $this->assertSame($playerArray['forceAutoplay'], $player->forceAutoplay);
        $this->assertSame($playerArray['hideTitle'], $player->hideTitle);
        $this->assertSame($playerArray['buttonTextInactive'], $player->buttonTextInactive);
        $this->assertSame($playerArray['buttonTextActive'], $player->buttonTextActive);
        $this->assertSame($playerArray['buttonTextHover'], $player->buttonTextHover);
        $this->assertSame($playerArray['buttonBackgroundTop'], $player->buttonBackgroundTop);
        $this->assertSame($playerArray['buttonBackgroundBottom'], $player->buttonBackgroundBottom);
        $this->assertSame($playerArray['buttonRadius'], $player->buttonRadius);
        $this->assertSame($playerArray['buttonLightEffect'], $player->buttonLightEffect);
        $this->assertSame($playerArray['controlMargin'], $player->controlMargin);
        $this->assertSame($playerArray['controlLogo'], $player->controlLogo);
        $this->assertSame($playerArray['controlLogoUrl'], $player->controlLogoUrl);
        $this->assertSame($playerArray['trackbarPlayedTop'], $player->trackbarPlayedTop);
        $this->assertSame($playerArray['trackbarPlayedBottom'], $player->trackbarPlayedBottom);
        $this->assertSame($playerArray['trackbarBackgroundTop'], $player->trackbarBackgroundTop);
        $this->assertSame($playerArray['trackbarBackgroundBottom'], $player->trackbarBackgroundBottom);
        $this->assertSame($playerArray['trackbarTextColor'], $player->trackbarTextColor);
        $this->assertSame($playerArray['panelTextInactive'], $player->panelTextInactive);
        $this->assertSame($playerArray['panelTextActive'], $player->panelTextActive);
        $this->assertSame($playerArray['panelTextHover'], $player->panelTextHover);
        $this->assertSame($playerArray['panelBackgroundTop'], $player->panelBackgroundTop);
        $this->assertSame($playerArray['panelBackgroundBottom'], $player->panelBackgroundBottom);
        $this->assertSame($playerArray['scrollbarThumb'], $player->scrollbarThumb);
        $this->assertSame($playerArray['scrollbarTrack'], $player->scrollbarTrack);
    }

    /**
     * @test
     */
    public function createWithropertiesSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $playerReturn = '
        {
            "playerId": "pl55mglWKqgywdX8Yu8WgDZ0",
            "buttonTextInactive":"rgba(255, 255, 255, 1)",
            "buttonTextActive":"rgba(255, 255, 255, 1)",
            "buttonTextHover":"rgba(255, 255, 255, 1)",
            "buttonBackgroundTop":"rgba(255, 255, 255, 1)",
            "buttonBackgroundBottom":"rgba(255, 255, 255, 1)",
            "buttonRadius":20,
            "buttonLightEffect":true,
            "controlMargin":"rgba(255, 255, 255, 1)",
            "controlLogo":null,
            "controlLogoUrl":null,
            "trackbarPlayedTop":"rgba(255, 255, 255, 1)",
            "trackbarPlayedBottom":"rgba(255, 255, 255, 1)",
            "trackbarBackgroundTop":"rgba(255, 255, 255, 1)",
            "trackbarBackgroundBottom":"rgba(255, 255, 255, 1)",
            "trackbarTextColor":"rgba(255, 255, 255, 1)",
            "panelTextInactive":"rgba(255, 255, 255, 1)",
            "panelTextActive":"rgba(255, 255, 255, 1)",
            "panelTextHover":"rgba(255, 255, 255, 1)",
            "panelBackgroundTop":"rgba(255, 255, 255, 1)",
            "panelBackgroundBottom":"rgba(255, 255, 255, 1)",
            "scrollbarThumb":"rgba(255, 255, 255, 1)",
            "scrollbarTrack":"rgba(255, 255, 255, 1)",
            "enableApi":false,"enableControls":true,
            "enableInfoPanel":true,
            "enableSharePanel":true,
            "enableDownloadPanel":true,
            "enableSettingsPanel":true,
            "forceAutoplay":true,
            "hideTitle":true
        }';

        $response = new Response();
        $response->setContent($playerReturn);

        $mockedBrowser->method('post')->willReturn($response);

        $players = new Players($mockedBrowser);
        /** @var Player $result */
        $player = $players->create(array("forceAutoplay" => true));
        $this->assertTrue($player->forceAutoplay);
    }

    /**
     * @test
     */
    public function updateSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $playerReturn = '
        {
            "playerId": "pl55mglWKqgywdX8Yu8WgDZ0",
            "buttonTextInactive":"rgba(255, 255, 255, 1)",
            "buttonTextActive":"rgba(255, 255, 255, 1)",
            "buttonTextHover":"rgba(255, 255, 255, 1)",
            "buttonBackgroundTop":"rgba(255, 255, 255, 1)",
            "buttonBackgroundBottom":"rgba(255, 255, 255, 1)",
            "buttonRadius":20,
            "buttonLightEffect":true,
            "controlMargin":"rgba(255, 255, 255, 1)",
            "controlLogo":null,
            "controlLogoUrl":null,
            "trackbarPlayedTop":"rgba(255, 255, 255, 1)",
            "trackbarPlayedBottom":"rgba(255, 255, 255, 1)",
            "trackbarBackgroundTop":"rgba(255, 255, 255, 1)",
            "trackbarBackgroundBottom":"rgba(255, 255, 255, 1)",
            "trackbarTextColor":"rgba(255, 255, 255, 1)",
            "panelTextInactive":"rgba(255, 255, 255, 1)",
            "panelTextActive":"rgba(255, 255, 255, 1)",
            "panelTextHover":"rgba(255, 255, 255, 1)",
            "panelBackgroundTop":"rgba(255, 255, 255, 1)",
            "panelBackgroundBottom":"rgba(255, 255, 255, 1)",
            "scrollbarThumb":"rgba(255, 255, 255, 1)",
            "scrollbarTrack":"rgba(255, 255, 255, 1)",
            "enableApi":false,"enableControls":true,
            "enableInfoPanel":true,
            "enableSharePanel":true,
            "enableDownloadPanel":true,
            "enableSettingsPanel":true,
            "forceAutoplay":false,
            "hideTitle":false
        }';

        $playerPatch = '
        {
            "playerId": "pl55mglWKqgywdX8Yu8WgDZ0",
            "buttonTextInactive":"rgba(255, 255, 255, 1)",
            "buttonTextActive":"rgba(255, 255, 255, 1)",
            "buttonTextHover":"rgba(255, 255, 255, 1)",
            "buttonBackgroundTop":"rgba(255, 255, 255, 1)",
            "buttonBackgroundBottom":"rgba(255, 255, 255, 1)",
            "buttonRadius":20,
            "buttonLightEffect":true,
            "controlMargin":"rgba(255, 255, 255, 1)",
            "controlLogo":null,
            "controlLogoUrl":null,
            "trackbarPlayedTop":"rgba(255, 255, 255, 1)",
            "trackbarPlayedBottom":"rgba(255, 255, 255, 1)",
            "trackbarBackgroundTop":"rgba(255, 255, 255, 1)",
            "trackbarBackgroundBottom":"rgba(255, 255, 255, 1)",
            "trackbarTextColor":"rgba(255, 255, 255, 1)",
            "panelTextInactive":"rgba(255, 255, 255, 1)",
            "panelTextActive":"rgba(255, 255, 255, 1)",
            "panelTextHover":"rgba(255, 255, 255, 1)",
            "panelBackgroundTop":"rgba(255, 255, 255, 1)",
            "panelBackgroundBottom":"rgba(255, 255, 255, 1)",
            "scrollbarThumb":"rgba(255, 255, 255, 1)",
            "scrollbarTrack":"rgba(255, 255, 255, 1)",
            "enableApi":false,"enableControls":true,
            "enableInfoPanel":true,
            "enableSharePanel":true,
            "enableDownloadPanel":true,
            "enableSettingsPanel":true,
            "forceAutoplay":true,
            "hideTitle":true
        }';

        $response = new Response();
        $response->setContent($playerReturn);

        $responsePatch = new Response();
        $responsePatch->setContent($playerPatch);

        $player = json_decode($playerReturn, true);
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
     */
    public function createWithTitleAndPropertiesSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": "DescrptionTest",
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "tags": ["tag1", "tag2"],
            "metadata": [],
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8"
            }
        }';

        $response = new Response();
        $response->setContent($videoReturn);

        $video = json_decode($videoReturn, true);
        $mockedBrowser->method('post')->willReturn($response);

        $videos = new Videos($mockedBrowser);
        /** @var Video $result */
        $result = $videos->create($video['title'], array('description' => $video['title'], 'tags' => $video['tags']));
        $this->assertSame($video['videoId'], $result->videoId);
        $this->assertSame($video['title'], $result->title);
        $this->assertSame($video['description'], $result->description);
        $this->assertSame($video['publishedAt'], $result->publishedAt->format(\DATE_ATOM));
        $this->assertSame($video['tags'], $result->tags);
        $this->assertSame($video['metadata'], $result->metadata);
        $this->assertSame($video['source'], $result->source);
        $this->assertSame($video['assets'], $result->assets);
    }

    private function getMockedOAuthBrowser()
    {
        return $this->getMockBuilder('ApiVideo\Client\Buzz\OAuthBrowser')
                    ->setMethods(array('get', 'submit', 'post', 'patch'))
                    ->getMock();
    }
}
