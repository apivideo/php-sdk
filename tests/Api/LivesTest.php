<?php

namespace ApiVideo\Client\Tests\Api;

use ApiVideo\Client\Api\Lives;
use ApiVideo\Client\Model\Live;
use Buzz\Message\Response;
use org\bovigo\vfs\content\LargeFileContent;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use InvalidArgumentException;

class LivesTest extends TestCase
{
    protected $filesystem;

    protected function setUp()
    {
        parent::setUp();
        $directory = array(
            'Live' => array(),
        );

        $this->filesystem = vfsStream::setup('root', 660, $directory);

    }

    /**
     * Tears down the fixture.
     */
    protected function tearDown()
    {
        unset($this->filesystem);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getSucceed()
    {
        $liveReturn = '
        {
            "liveStreamId": "li55mglWKqgywdX8Yu8WgDZ0",
            "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
            "name": "Test",
            "record": false,
            "broadcasting": false,
            "public": false,
            "assets": {
                "iframe": "<iframe src=\"https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                "player": "https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0",
                "hls": "https://live.api.video/li55mglWKqgywdX8Yu8WgDZ0.m3u8",
                "thumbnail": "https://cdn.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
            }
        }';


        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($liveReturn));


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $lives = new Lives($oAuthBrowser);
        $live  = $lives->get('li55mglWKqgywdX8Yu8WgDZ0');

        $this->assertInstanceOf('ApiVideo\Client\Model\Live', $live);
        $this->assertSame('li55mglWKqgywdX8Yu8WgDZ0', $live->liveStreamId);
        $this->assertSame('Test', $live->name);
        $this->assertFalse($live->record);
        $this->assertFalse($live->broadcasting);
        $this->assertFalse($live->public);
        $this->assertSame(
            'https://live.api.video/li55mglWKqgywdX8Yu8WgDZ0.m3u8',
            $live->assets['hls']
        );
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

        $lives = new Lives($oAuthBrowser);
        $live  = $lives->get('li55mglWKqgywdX8Yu8WgDZ0');

        $this->assertNull($live);
        $error = $lives->getLastError();

        $this->assertSame(404, $error['status']);
        $this->assertEmpty($error['message']);

    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function searchSucceed()
    {
        $liveReturn1 = '
        {
            "data": [
                {
                    "liveStreamId": "li55mglWKqgywdX8Yu8WgDZ0",
                    "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
                    "name": "Test",
                    "record": false,
                    "broadcasting": false,
                    "public": true,
                    "assets": {
                        "iframe": "<iframe src=\"https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                        "player": "https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0",
                        "hls": "https://live.api.video/li55mglWKqgywdX8Yu8WgDZ0.m3u8",
                        "thumbnail": "https://cdn.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
                    }
                }
            ],
            "pagination": {
                "currentPage": 1,
                "pageSize": 1,
                "pagesTotal": 10,
                "itemsTotal": 10,
                "currentPageItems": 1,
                "links": [
                     {
                        "rel": "self",
                        "uri": "/lives-streams?pageSize=1&sortBy=name&sortOrder=asc"
                     },
                     {
                        "rel": "first",
                        "uri": "/lives-streams?pageSize=1&sortBy=name&sortOrder=asc"
                     },
                     {
                        "rel": "next",
                        "uri": "/lives-streams?currentPage=2&pageSize=1&sortBy=name&sortOrder=asc"
                     },
                     {
                        "rel": "last",
                        "uri": "/lives-streams?currentPage=10&pageSize=1&sortBy=name&sortOrder=asc"
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
        $setContent->invokeArgs($response, array($liveReturn1));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $lives  = new Lives($oAuthBrowser);
        $results = $lives->search(
            array(
                'currentPage' => 1,
                'pageSize'    => 1,
                'sortBy'      => 'title',
                'sortOrder'   => 'asc',
            )
        );

        $livesReflected = new ReflectionClass('ApiVideo\Client\Api\Lives');
        $castAll         = $livesReflected->getMethod('castAll');
        $castAll->setAccessible(true);

        $livesReturn = json_decode($liveReturn1, true);

        $this->assertEquals(array_merge(array(), $castAll->invokeArgs($lives, $livesReturn)), $results);


    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function searchWithBadSortByParametersShouldFailed()
    {
        $return = '{
            "status": 400,
            "type": "https://docs.api.Live/problems/attribute.invalid",
            "title": "An attribute is invalid.",
            "name": "",
            "details": "Sorting by \'toto\' is not allowed. Allowed values: name"
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

        $lives  = new Lives($oAuthBrowser);
        $results = $lives->search(
            array(
                'currentPage' => 1,
                'pageSize'    => 25,
                'sortBy'      => 'toto',
                'sortOrder'   => 'asc',
            )
        );
        $this->assertNull($results);
        $error = $lives->getLastError();

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
        $liveReturn = '
        {
            "data": [
                {
                    "liveStreamId": "li55mglWKqgywdX8Yu8WgDZ0",
                    "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
                    "name": "Test",
                    "record": false,
                    "broadcasting": false,
                    "public": true,
                    "assets": {
                        "iframe": "<iframe src=\"https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                        "player": "https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0",
                        "hls": "https://live.api.video/li55mglWKqgywdX8Yu8WgDZ0.m3u8",
                        "thumbnail": "https://cdn.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
                    }
                },
                {
                    "liveStreamId": "li85mglWKqgywdX8Yu8WgDZ0",
                    "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
                    "name": "Test2",
                    "record": false,
                    "broadcasting": false,
                    "public": true,
                    "assets": {
                        "iframe": "<iframe src=\"https://embed.api.video/live-streams/li85mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                        "player": "https://embed.api.video/live-streams/li85mglWKqgywdX8Yu8WgDZ0",
                        "hls": "https://live.api.video/li85mglWKqgywdX8Yu8WgDZ0.m3u8",
                        "thumbnail": "https://cdn.api.video/live-streams/li85mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
                    }
                },
                {
                    "liveStreamId": "li87mglWKqgywdX8Yu8WgDZ0",
                    "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
                    "name": "Test3",
                    "record": false,
                    "broadcasting": false,
                    "public": true,
                    "assets": {
                        "iframe": "<iframe src=\"https://embed.api.video/live-streams/li87mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                        "player": "https://embed.api.video/live-streams/li87mglWKqgywdX8Yu8WgDZ0",
                        "hls": "https://live.api.video/li87mglWKqgywdX8Yu8WgDZ0.m3u8",
                        "thumbnail": "https://cdn.api.video/live-streams/li87mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
                    }
                }
            ],
            "pagination": {
                "currentPage": 1,
                "pageSize": 25,
                "pagesTotal": 1,
                "itemsTotal": 3,
                "currentPageItems": 3,
                "links": [
                    {
                        "rel": "self",
                        "uri": "/live-streams?pageSize=25&sortBy=name&sortOrder=asc"
                    },
                    {
                        "rel": "first",
                        "uri": "/live-streams?pageSize=25&sortBy=name&sortOrder=asc"
                    },
                    {
                        "rel": "last",
                        "uri": "/live-streams?pageSize=25&sortBy=name&sortOrder=asc"
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
        $setContent->invokeArgs($response, array($liveReturn));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $lives  = new Lives($oAuthBrowser);

        $class = $this;
        $callback = function($live) use ($class){
            $class->assertNotNull($live->liveStreamId);
        };

        $lives->search(
            array(
                'currentPage' => 1,
                'pageSize'    => 25,
                'sortBy'      => 'toto',
                'sortOrder'   => 'asc',
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
        $liveReturn = '
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
                        "rel": "first",
                        "uri": "/live-streams?pageSize=25&sortBy=name&sortOrder=asc"
                    },
                    {
                        "rel": "last",
                        "uri": "/live-streams?pageSize=25&sortBy=name&sortOrder=asc"
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
        $setContent->invokeArgs($response, array($liveReturn));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $lives  = new Lives($oAuthBrowser);
        $results = $lives->search(
            array(
                'currentPage' => 1,
                'pageSize'    => 25,
                'sortBy'      => 'name',
                'sortOrder'   => 'asc',
            )
        );

        $livesReflected = new ReflectionClass('ApiVideo\Client\Api\Lives');
        $castAll         = $livesReflected->getMethod('castAll');
        $castAll->setAccessible(true);

        $livesReturn = json_decode($liveReturn, true);

        $this->assertEquals(array_merge(array(), $castAll->invokeArgs($lives, $livesReturn)), $results);


    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function searchWithoutPaginationShouldReturnFirstPageWith100Items()
    {
        $liveReturn = '
        {
            "data": [
                {
                    "liveStreamId": "li55mglWKqgywdX8Yu8WgDZ0",
                    "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
                    "name": "Test",
                    "record": false,
                    "broadcasting": false,
                    "public": true,
                    "assets": {
                        "iframe": "<iframe src=\"https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                        "player": "https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0",
                        "hls": "https://live.api.video/li55mglWKqgywdX8Yu8WgDZ0.m3u8",
                        "thumbnail": "https://cdn.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
                    }
                },
                {
                    "liveStreamId": "li85mglWKqgywdX8Yu8WgDZ0",
                    "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
                    "name": "Test2",
                    "record": false,
                    "broadcasting": false,
                    "public": true,
                    "assets": {
                        "iframe": "<iframe src=\"https://embed.api.video/live-streams/li85mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                        "player": "https://embed.api.video/live-streams/li85mglWKqgywdX8Yu8WgDZ0",
                        "hls": "https://live.api.video/li85mglWKqgywdX8Yu8WgDZ0.m3u8",
                        "thumbnail": "https://cdn.api.video/live-streams/li85mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
                    }
                },
                {
                    "liveStreamId": "li87mglWKqgywdX8Yu8WgDZ0",
                    "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
                    "name": "Test3",
                    "record": false,
                    "broadcasting": false,
                    "public": true,
                    "assets": {
                        "iframe": "<iframe src=\"https://embed.api.video/live-streams/li87mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                        "player": "https://embed.api.video/live-streams/li87mglWKqgywdX8Yu8WgDZ0",
                        "hls": "https://live.api.video/li87mglWKqgywdX8Yu8WgDZ0.m3u8",
                        "thumbnail": "https://cdn.api.video/live-streams/li87mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
                    }
                }
            ],
            "pagination": {
                "currentPage": 1,
                "pageSize": 100,
                "pagesTotal": 1,
                "itemsTotal": 3,
                "currentPageItems": 3,
                "links": [
                    {
                        "rel": "self",
                        "uri": "/live-streams?pageSize=100&sortBy=name&sortOrder=asc"
                    },
                    {
                        "rel": "first",
                        "uri": "/live-streams?pageSize=100&sortBy=name&sortOrder=asc"
                    },
                    {
                        "rel": "last",
                        "uri": "/live-streams?pageSize=100&sortBy=name&sortOrder=asc"
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
        $setContent->invokeArgs($response, array($liveReturn));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $lives  = new Lives($oAuthBrowser);
        $results = $lives->search();

        $livesReflected = new ReflectionClass('ApiVideo\Client\Api\Lives');
        $castAll         = $livesReflected->getMethod('castAll');
        $castAll->setAccessible(true);

        $livesReturn = json_decode($liveReturn, true);

        $this->assertEquals(array_merge(array(), $castAll->invokeArgs($lives, $livesReturn)), $results);

    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function createWithNameSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $liveReturn = '
        {
            "liveStreamId": "li55mglWKqgywdX8Yu8WgDZ0",
            "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
            "name": "Test",
            "record": false,
            "broadcasting": false,
            "public": true,
            "assets": {
                "iframe": "<iframe src=\"https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                "player": "https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0",
                "hls": "https://live.api.video/li55mglWKqgywdX8Yu8WgDZ0.m3u8",
                "thumbnail": "https://cdn.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($liveReturn));

        $live = json_decode($liveReturn, true);
        $mockedBrowser->method('post')->willReturn($response);

        $lives = new Lives($mockedBrowser);
        /** @var Live $result */
        $result = $lives->create($live['name']);
        $this->assertSame($live['liveStreamId'], $result->liveStreamId);
        $this->assertSame($live['name'], $result->name);
        $this->assertSame($live['streamKey'], $result->streamKey);
        $this->assertSame($live['record'], $result->record);
        $this->assertFalse($result->record);
        $this->assertSame($live['broadcasting'], $result->record);
        $this->assertFalse($result->broadcasting);
        $this->assertSame($live['assets'], $result->assets);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function updateSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $liveReturn = '
        {
            "liveStreamId": "li55mglWKqgywdX8Yu8WgDZ0",
            "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
            "name": "Test",
            "record": false,
            "broadcasting": false,
            "public": true,
            "assets": {
                "iframe": "<iframe src=\"https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                "player": "https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0",
                "hls": "https://live.api.video/li55mglWKqgywdX8Yu8WgDZ0.m3u8",
                "thumbnail": "https://cdn.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
            }
        }';

        $livePatch = '
        {
            "liveStreamId": "li55mglWKqgywdX8Yu8WgDZ0",
            "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
            "name": "Test",
            "record": false,
            "broadcasting": true,
            "public": true,
            "assets": {
                "iframe": "<iframe src=\"https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                "player": "https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0",
                "hls": "https://live.api.video/li55mglWKqgywdX8Yu8WgDZ0.m3u8",
                "thumbnail": "https://cdn.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($liveReturn));

        $responsePatch = new Response();

        $responsePatchReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCodePatch        = $responsePatchReflected->getProperty('statusCode');
        $statusCodePatch->setAccessible(true);
        $statusCodePatch->setValue($responsePatch, 200);
        $setContentPatch = $responsePatchReflected->getMethod('setContent');
        $setContentPatch->invokeArgs($responsePatch, array($livePatch));

        $live = json_decode($liveReturn, true);

        $mockedBrowser->method('post')->willReturn($response);
        $mockedBrowser->method('patch')->willReturn($responsePatch);

        $lives = new Lives($mockedBrowser);
        /** @var Live $result */
        $result = $lives->create($live['name']);
        $this->assertFalse($result->broadcasting);
        $properties = array('broadcasting' => true);
        $resultPath = $lives->update($result->liveStreamId, $properties);
        $this->assertTrue($resultPath->broadcasting);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function uploadThumbnailSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $liveReturn = '
        {
            "liveStreamId": "li55mglWKqgywdX8Yu8WgDZ0",
            "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
            "name": "Test",
            "record": false,
            "broadcasting": false,
            "public": true,
            "assets": {
                "iframe": "<iframe src=\"https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                "player": "https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0",
                "hls": "https://live.api.video/li55mglWKqgywdX8Yu8WgDZ0.m3u8",
                "thumbnail": "https://cdn.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($liveReturn));

        $live = json_decode($liveReturn, true);
        $mockedBrowser->method('submit')->willReturn($response);

        $lives = new Lives($mockedBrowser);
        /** @var Live $result */
        $result = $lives->uploadThumbnail($this->getValideThumbnail()->url(), $live['liveStreamId']);
        $this->assertSame($live['liveStreamId'], $result->liveStreamId);
        $this->assertSame($live['name'], $result->name);
        $this->assertSame($live['streamKey'], $result->streamKey);
        $this->assertSame($live['record'], $result->record);
        $this->assertFalse($result->record);
        $this->assertSame($live['broadcasting'], $result->record);
        $this->assertFalse($result->broadcasting);
        $this->assertSame($live['assets'], $result->assets);
    }
    /**
     * @test
     * @throws \ReflectionException
     */
    public function uploadThumbnailPngFailed()
    {
        $return = '{
            "status": 400,
            "type": "https://docs.api.Live/problems/file.extension",
            "title": "Only [jpeg, jpg, JPG, JPEG] extensions are supported.",
            "name": "file"
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 400);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($return));

        $mockedBrowser = $this->getMockedOAuthBrowser();

        $mockedBrowser->method('submit')->willReturn($response);

        $lives = new Lives($mockedBrowser);
        /** @var Live $result */
        $result = $lives->uploadThumbnail($this->getInvalideImage()->url(), 'li55mglWKqgywdX8Yu8WgDZ0');
        $this->assertNull($result);
        $error = $lives->getLastError();

        $this->assertSame(400, $error['status']);
        $return = json_decode($return, true);
        $this->assertSame($return, $error['message']);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The source file must be readable.
     */
    public function uploadThumbnailWithBadSourceShouldFail()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();
        $liveReturn   = '
        {
            "liveStreamId": "li55mglWKqgywdX8Yu8WgDZ0",
            "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
            "name": "Test",
            "record": false,
            "broadcasting": false,
            "public": true,
            "assets": {
                "iframe": "<iframe src=\"https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                "player": "https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0",
                "hls": "https://live.api.video/li55mglWKqgywdX8Yu8WgDZ0.m3u8",
                "thumbnail": "https://cdn.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
            }
        }';
        $live         = json_decode($liveReturn, true);
        $lives        = new Lives($mockedBrowser);

        $lives->uploadThumbnail($this->filesystem->url().'/image/testfail.jpg', $live['liveStreamId']);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage 'vfs://root/testempty.jpg' is an empty file.
     * @throws \ReflectionException
     */
    public function uploadThumbnailWithEmptySourceShouldFail()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $liveReturn = '
        {
            "liveStreamId": "li55mglWKqgywdX8Yu8WgDZ0",
            "streamKey": "1a882528-1208-46f1-830c-b13be7995adf",
            "name": "Test",
            "record": false,
            "broadcasting": false,
            "public": true,
            "assets": {
                "iframe": "<iframe src=\"https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0\" width=\"100%\" height=\"100%\" frameborder=\"0\" scrolling=\"no\" allowfullscreen=\"\"></iframe>",
                "player": "https://embed.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0",
                "hls": "https://live.api.video/li55mglWKqgywdX8Yu8WgDZ0.m3u8",
                "thumbnail": "https://cdn.api.video/live-streams/li55mglWKqgywdX8Yu8WgDZ0/thumbnail.jpg"
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($liveReturn));

        $live = json_decode($liveReturn, true);

        $mockedBrowser->method('post')->willReturn($response);

        $lives = new Lives($mockedBrowser);

        $lives->uploadThumbnail($this->getInvalidThumbnail()->url(), $live['liveStreamId']);
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

        $lives = new Lives($mockedBrowser);

        $status = $lives->delete('li55mglWKqgywdX8Yu8WgDZ0');
        $this->assertSame(204, $status);
    }



    /**
     * @test
     * @throws \ReflectionException
     */
    public function deleteWithBadliveStreamIdFailed()
    {
        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 404);

        $mockedBrowser = $this->getMockedOAuthBrowser();
        $mockedBrowser->method('delete')->willReturn($response);

        $lives = new Lives($mockedBrowser);
        $result = $lives->delete('dg55mglWKqgywdX8Yu8WgDZ0');

        $this->assertNull($result);
        $error = $lives->getLastError();

        $this->assertSame(404, $error['status']);
    }


    private function getMockedOAuthBrowser()
    {
        return $this->getMockBuilder('ApiVideo\Client\Buzz\OAuthBrowser')
                    ->setMethods(array('get', 'submit', 'post', 'patch', 'delete'))
                    ->getMock();
    }



    private function getInvalideImage()
    {

        return vfsStream::newFile('test.png')
                        ->withContent(LargeFileContent::withKilobytes(200))
                        ->at($this->filesystem);
    }




    private function getValideThumbnail()
    {

        return vfsStream::newFile('test.jpg')
                        ->withContent(LargeFileContent::withKilobytes(300))
                        ->at($this->filesystem);
    }

    private function getInvalidThumbnail()
    {

        return vfsStream::newFile('testempty.jpg')
                        ->withContent(LargeFileContent::withKilobytes(0))
                        ->at($this->filesystem);
    }
}
