<?php

namespace ApiVideo\Client\Tests\Api;

use ApiVideo\Client\Api\Videos;
use ApiVideo\Client\Model\Video;
use Buzz\Message\Response;
use org\bovigo\vfs\content\LargeFileContent;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use InvalidArgumentException;

class VideosTest extends TestCase
{
    protected $filesystem;

    /**
     * @return string
     */
    private static function buildVideoResponse()
    {
        return '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": "",
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "updatedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
            "metadata": [],
            "public": true,
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
            },
            "panoramic": false,
            "mp4Support": false
        }';
    }

    /**
     * @return string
     */
    private static function buildVideoPatchBody()
    {
        return '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": "Description test",
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "updatedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
"metadata": [],
            "public": true,
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
            },
            "panoramic": false,
            "mp4Support": false
        }';
    }

    protected function setUp()
    {
        parent::setUp();
        $directory = array(
            'video' => array(),
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
        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "Test",
            "description": null,
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "updatedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
"metadata": [],
            "public": true,
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
            },
            "panoramic": false,
            "mp4Support": false
        }';


        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $videos = new Videos($oAuthBrowser);
        $video  = $videos->get('vi55mglWKqgywdX8Yu8WgDZ0');

        $this->assertInstanceOf('ApiVideo\Client\Model\Video', $video);
        $this->assertSame('vi55mglWKqgywdX8Yu8WgDZ0', $video->videoId);
        $this->assertSame('Test', $video->title);
        $this->assertNull($video->description);
        $this->assertEquals(
            \DateTimeImmutable::createFromFormat(\DateTime::ATOM, '2018-05-18T17:21:11+02:00'),
            $video->publishedAt
        );
        $this->assertEmpty($video->tags);
        $this->assertEmpty($video->metadata);
        $this->assertSame('/videos/vi55mglWKqgywdX8Yu8WgDZ0/source', $video->source['uri']);
        $this->assertSame(
            'https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8',
            $video->assets['hls']
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

        $videos = new Videos($oAuthBrowser);
        $video  = $videos->get('viWKqgywdX55mgl8Yu8WgDZ0');

        $this->assertNull($video);
        $error = $videos->getLastError();

        $this->assertSame(404, $error['status']);
        $this->assertEmpty($error['message']);

    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getStatusSucceed()
    {
        $statusReturn = '{
    "ingest": {
        "status": "uploaded",
        "filesize": 6732820,
        "receivedBytes": []
    },
    "encoding": {
        "playable": true,
        "qualities": [
            {
                "type": "hls",
                "quality": "240p",
                "status": "encoded"
            },
            {
                "type": "hls",
                "quality": "360p",
                "status": "encoded"
            }
        ],
        "metadata": {
            "width": 1080,
            "height": 1920,
            "bitrate": 3995,
            "duration": 13,
            "framerate": 24000,
            "samplerate": 48000,
            "videoCodec": "h264",
            "audioCodec": "aac",
            "aspectRatio": ""
        }
    }
}';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($statusReturn));


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $videos = new Videos($oAuthBrowser);
        $video  = $videos->getStatus('vi55mglWKqgywdX8Yu8WgDZ0');

        $this->assertInstanceOf('ApiVideo\Client\Model\VideoStatus', $video);
        $this->assertSame(6732820, $video->ingest->fileSize);
        $this->assertSame('uploaded', $video->ingest->status);
        $this->assertSame(array(), $video->ingest->receivedBytes);

        $this->assertSame(true, $video->encoding->playable);
        $this->assertSame('hls', $video->encoding->qualities[0]['type']);
        $this->assertSame('240p', $video->encoding->qualities[0]['quality']);
        $this->assertSame('encoded', $video->encoding->qualities[0]['status']);
        $this->assertSame('hls', $video->encoding->qualities[1]['type']);
        $this->assertSame('360p', $video->encoding->qualities[1]['quality']);
        $this->assertSame('encoded', $video->encoding->qualities[1]['status']);
        $this->assertSame(1080, $video->encoding->metadata['width']);
        $this->assertSame(1920, $video->encoding->metadata['height']);
        $this->assertSame(3995, $video->encoding->metadata['bitrate']);
        $this->assertSame(13, $video->encoding->metadata['duration']);
        $this->assertSame(24000, $video->encoding->metadata['framerate']);
        $this->assertSame(48000, $video->encoding->metadata['samplerate']);
        $this->assertSame("h264", $video->encoding->metadata['videoCodec']);
        $this->assertSame("aac", $video->encoding->metadata['audioCodec']);
        $this->assertSame("", $video->encoding->metadata['aspectRatio']);

    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getStatusNoUpload()
    {
        $statusReturn = '{
    "ingest": [],
    "encoding": {
        "playable": false,
        "qualities": [],
        "metadata": {
            "width": null,
            "height": null,
            "bitrate": null,
            "duration": null,
            "framerate": null,
            "samplerate": null,
            "videoCodec": null,
            "audioCodec": null,
            "aspectRatio": null
        }
    }
}';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($statusReturn));


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $videos = new Videos($oAuthBrowser);
        $video  = $videos->getStatus('vi55mglWKqgywdX8Yu8WgDZ0');

        $this->assertInstanceOf('ApiVideo\Client\Model\VideoStatus', $video);
        $this->assertNull($video->ingest);

        $this->assertSame(false, $video->encoding->playable);
        $this->assertSame(array(), $video->encoding->qualities);
        $this->assertNull($video->encoding->metadata['width']);
        $this->assertNull($video->encoding->metadata['height']);
        $this->assertNull($video->encoding->metadata['bitrate']);
        $this->assertNull($video->encoding->metadata['duration']);
        $this->assertNull($video->encoding->metadata['framerate']);
        $this->assertNull($video->encoding->metadata['samplerate']);
        $this->assertNull($video->encoding->metadata['videoCodec']);
        $this->assertNull($video->encoding->metadata['audioCodec']);
        $this->assertNull($video->encoding->metadata['aspectRatio']);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function searchSucceed()
    {
        $videoReturn1 = '
        {
            "data": [
                {
                    "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
                    "title": "Rosetta",
                    "description": null,
                    "publishedAt": "2018-05-03T17:21:11+02:00",
                    "updatedAt": "2018-05-18T17:21:11+02:00",
                    "tags": [],
                    "public": true,
                    "metadata": [],
                    "source": {
                        "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
                    },
                    "assets": {
                        "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                        "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                        "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                        "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
                    },
                    "panoramic": false,
                    "mp4Support": false
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
                        "uri": "/videos?pageSize=1&sortBy=title&sortOrder=asc"
                     },
                     {
                        "rel": "first",
                        "uri": "/videos?pageSize=1&sortBy=title&sortOrder=asc"
                     },
                     {
                        "rel": "next",
                        "uri": "/videos?currentPage=2&pageSize=1&sortBy=title&sortOrder=asc"
                     },
                     {
                        "rel": "last",
                        "uri": "/videos?currentPage=10&pageSize=1&sortBy=title&sortOrder=asc"
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
        $setContent->invokeArgs($response, array($videoReturn1));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $videos  = new Videos($oAuthBrowser);
        $results = $videos->search(
            array(
                'currentPage' => 1,
                'pageSize'    => 1,
                'sortBy'      => 'title',
                'sortOrder'   => 'asc',
            )
        );

        $videosReflected = new ReflectionClass('ApiVideo\Client\Api\Videos');
        $castAll         = $videosReflected->getMethod('castAll');
        $castAll->setAccessible(true);

        $videosReturn = json_decode($videoReturn1, true);

        $this->assertEquals(array_merge(array(), $castAll->invokeArgs($videos, $videosReturn)), $results);


    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function searchWithBadSortByParametersShouldFailed()
    {
        $return = '{
            "status": 400,
            "type": "https://docs.api.video/problems/attribute.invalid",
            "title": "An attribute is invalid.",
            "name": "",
            "details": "Sorting by \'toto\' is not allowed. Allowed values: title, publishedAt"
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

        $videos  = new Videos($oAuthBrowser);
        $results = $videos->search(
            array(
                'currentPage' => 1,
                'pageSize'    => 25,
                'sortBy'      => 'toto',
                'sortOrder'   => 'asc',
            )
        );
        $this->assertNull($results);
        $error = $videos->getLastError();

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
        $videoReturn = '
        {
            "data": [
                {
                    "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
                    "title": "Rosetta",
                    "description": null,
                    "publishedAt": "2018-05-03T17:21:11+02:00",
                    "updatedAt": "2018-05-18T17:21:11+02:00",
                    "tags": [],
                    "metadata": [],
                    "public": true,
                    "source": {
                        "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
                    },
                    "assets": {
                        "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                        "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                        "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                        "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
                    },
                    "panoramic": false,
                    "mp4Support": false
                },
                {
                    "videoId": "viABC",
                    "title": "Test ABC",
                    "description": "Description ABAC",
                    "publishedAt": "2018-05-03T17:21:12+02:00",
                    "updatedAt": "2018-05-18T17:21:11+02:00",
                    "tags": [
                        "a",
                        "b",
                        "c"
                    ],
                    "metadata": {
                        "baz": "wat",
                        "foo": "bar"
                    },
                    "public": true,
                    "source": {
                        "uri": "/videos/viABC/source"
                    },
                    "assets": {
                        "iframe": "<iframe src=\'https://embed.api.video/viABC?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                        "player": "https://embed.api.video/viABC?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                        "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                        "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
                    },
                    "panoramic": false,
                    "mp4Support": false
                },
                {
                    "videoId": "viDEF",
                    "title": "Test DEF",
                    "description": "Description DEF",
                    "publishedAt": "2018-05-03T17:21:13+02:00",
                    "updatedAt": "2018-05-18T17:21:11+02:00",
                    "tags": [
                        "c",
                        "d",
                        "e"
                    ],
                    "metadata": {
                        "baz": "wat",
                        "alice": "bob"
                    },
                    "public": true,
                    "source": {
                        "uri": "/videos/viDEF/source"
                    },
                    "assets": {
                        "iframe": "<iframe src=\'https://embed.api.video/viDEF?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                        "player": "https://embed.api.video/viDEF?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                        "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                        "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
                    },
                    "panoramic": false,
                    "mp4Support": false
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
                        "uri": "/videos?pageSize=25&sortBy=title&sortOrder=asc"
                    },
                    {
                        "rel": "first",
                        "uri": "/videos?pageSize=25&sortBy=title&sortOrder=asc"
                    },
                    {
                        "rel": "last",
                        "uri": "/videos?pageSize=25&sortBy=title&sortOrder=asc"
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
        $setContent->invokeArgs($response, array($videoReturn));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $videos  = new Videos($oAuthBrowser);

        $class = $this;
        $callback = function($video) use ($class){
            $class->assertNotNull($video->videoId);
        };

        $videos->search(
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
        $videoReturn = '
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
                        "uri": "/videos?pageSize=25&sortBy=title&sortOrder=asc"
                    },
                    {
                        "rel": "last",
                        "uri": "/videos?pageSize=25&sortBy=title&sortOrder=asc"
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
        $setContent->invokeArgs($response, array($videoReturn));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $videos  = new Videos($oAuthBrowser);
        $results = $videos->search(
            array(
                'currentPage' => 1,
                'pageSize'    => 25,
                'sortBy'      => 'title',
                'sortOrder'   => 'asc',
            )
        );

        $videosReflected = new ReflectionClass('ApiVideo\Client\Api\Videos');
        $castAll         = $videosReflected->getMethod('castAll');
        $castAll->setAccessible(true);

        $videosReturn = json_decode($videoReturn, true);

        $this->assertEquals(array_merge(array(), $castAll->invokeArgs($videos, $videosReturn)), $results);


    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function searchWithoutPaginationShouldReturnFirstPageWith100Items()
    {
        $videoReturn = '
        {
            "data": [
                {
                    "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
                    "title": "Rosetta",
                    "description": null,
                    "publishedAt": "2018-05-03T17:21:11+02:00",
                    "updatedAt": "2018-05-18T17:21:11+02:00",
                    "tags": [],
                    "metadata": [],
                    "public": true,
                    "source": {
                        "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
                    },
                    "assets": {
                        "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                        "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                        "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                        "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
                    },
                    "panoramic": false,
                    "mp4Support": false
                },
                {
                    "videoId": "viABC",
                    "title": "Test ABC",
                    "description": "Description ABAC",
                    "publishedAt": "2018-05-03T17:21:12+02:00",
                    "updatedAt": "2018-05-18T17:21:11+02:00",
                    "tags": [
                        "a",
                        "b",
                        "c"
                    ],
                    "metadata": {
                        "baz": "wat",
                        "foo": "bar"
                    },
                    "public": true,
                    "source": {
                        "uri": "/videos/viABC/source"
                    },
                    "assets": {
                        "iframe": "<iframe src=\'https://embed.api.video/viABC?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                        "player": "https://embed.api.video/viABC?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                        "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                        "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
                    },
                    "panoramic": false,
                    "mp4Support": false
                },
                {
                    "videoId": "viDEF",
                    "title": "Test DEF",
                    "description": "Description DEF",
                    "publishedAt": "2018-05-03T17:21:13+02:00",
                    "updatedAt": "2018-05-18T17:21:11+02:00",
                    "tags": [
                        "c",
                        "d",
                        "e"
                    ],
                    "metadata": {
                        "baz": "wat",
                        "alice": "bob"
                    },
                    "public": true,
                    "source": {
                        "uri": "/videos/viDEF/source"
                    },
                    "assets": {
                        "iframe": "<iframe src=\'https://embed.api.video/viDEF?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                        "player": "https://embed.api.video/viDEF?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                        "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                        "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
                    },
                    "panoramic": false,
                    "mp4Support": false
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
                        "uri": "/videos?pageSize=100&sortBy=title&sortOrder=asc"
                    },
                    {
                        "rel": "first",
                        "uri": "/videos?pageSize=100&sortBy=title&sortOrder=asc"
                    },
                    {
                        "rel": "last",
                        "uri": "/videos?pageSize=100&sortBy=title&sortOrder=asc"
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
        $setContent->invokeArgs($response, array($videoReturn));

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $videos  = new Videos($oAuthBrowser);
        $results = $videos->search();

        $videosReflected = new ReflectionClass('ApiVideo\Client\Api\Videos');
        $castAll         = $videosReflected->getMethod('castAll');
        $castAll->setAccessible(true);

        $videosReturn = json_decode($videoReturn, true);

        $this->assertEquals(array_merge(array(), $castAll->invokeArgs($videos, $videosReturn)), $results);

    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function createWithTitleSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": null,
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "updatedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
"metadata": [],
            "public": true,
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
            },
            "panoramic": false,
            "mp4Support": false
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));

        $video = json_decode($videoReturn, true);
        $mockedBrowser->method('post')->willReturn($response);

        $videos = new Videos($mockedBrowser);
        /** @var Video $result */
        $result = $videos->create($video['title']);
        $this->assertSame($video['videoId'], $result->videoId);
        $this->assertSame($video['title'], $result->title);
        $this->assertSame($video['description'], $result->description);
        $this->assertSame($video['publishedAt'], $result->publishedAt->format(\DATE_ATOM));
        $this->assertSame($video['tags'], $result->tags);
        $this->assertSame($video['metadata'], $result->metadata);
        $this->assertSame($video['source'], $result->source);
        $this->assertSame($video['assets'], $result->assets);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function createWithBadPublishedAtShouldFailed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $return = '{
            "status": 400,
            "type": "https://docs.api.video/problems/attribute.invalid",
            "title": "The attribute must be a ISO8601 date.",
            "name": "publishedAt"
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 400);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($return));

        $mockedBrowser->method('post')->willReturn($response);

        $videos = new Videos($mockedBrowser);
        /** @var Video $result */
        $result = $videos->create('test', array('publishedAt' => 'salut'));

        $this->assertNull($result);
        $error = $videos->getLastError();

        $this->assertSame(400, $error['status']);
        $return = json_decode($return, true);
        $this->assertSame($return, $error['message']);
    }

    /**
     * @test
     * @throws \ReflectionException
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
            "updatedAt": "2018-05-18T17:21:11+02:00",
            "tags": ["tag1", "tag2"],
            "metadata": [],
            "public": true,
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
            },
            "panoramic": false,
            "mp4Support": false
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));

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

    /**
     * @test
     * @throws \ReflectionException
     */
    public function updateSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videoReturn = self::buildVideoResponse();

        $videoPatch = self::buildVideoPatchBody();

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));

        $responsePatch = new Response();

        $responsePatchReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCodePatch        = $responsePatchReflected->getProperty('statusCode');
        $statusCodePatch->setAccessible(true);
        $statusCodePatch->setValue($responsePatch, 200);
        $setContentPatch = $responsePatchReflected->getMethod('setContent');
        $setContentPatch->invokeArgs($responsePatch, array($videoPatch));

        $video = json_decode($videoReturn, true);

        $mockedBrowser->method('post')->willReturn($response);
        $mockedBrowser->method('patch')->willReturn($responsePatch);

        $videos = new Videos($mockedBrowser);
        /** @var Video $result */
        $result = $videos->create($video['title']);
        $this->assertEmpty($result->description);
        $properties = array('description' => 'Description test');
        $resultPath = $videos->update($result->videoId, $properties);
        $this->assertSame('Description test', $resultPath->description);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function updateWihBadPublishedAtFailed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $return = '{
            "status": 400,
            "type": "https://docs.api.video/problems/attribute.invalid",
            "title": "The attribute must be a ISO8601 date.",
            "name": "publishedAt"
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 400);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($return));

        $mockedBrowser->method('patch')->willReturn($response);

        $videos = new Videos($mockedBrowser);
        /** @var Video $result */
        $result = $videos->update('vi55mglWKqgywdX8Yu8WgDZ0', array('publishedAt' => 'salut'));

        $this->assertNull($result);
        $error = $videos->getLastError();

        $this->assertSame(400, $error['status']);
        $return = json_decode($return, true);
        $this->assertSame($return, $error['message']);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function updateThumbnailWithTimeCodeSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videoReturn = self::buildVideoResponse();

        $videoPatch = self::buildVideoPatchBody();

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));

        $responsePatch = new Response();

        $responsePatchReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCodePatch        = $responsePatchReflected->getProperty('statusCode');
        $statusCodePatch->setAccessible(true);
        $statusCodePatch->setValue($responsePatch, 200);
        $setContentPatch = $responsePatchReflected->getMethod('setContent');
        $setContentPatch->invokeArgs($responsePatch, array($videoPatch));

        $video      = json_decode($videoReturn, true);
        $videoPatch = json_decode($videoPatch, true);

        $mockedBrowser->method('post')->willReturn($response);
        $mockedBrowser->method('patch')->willReturn($responsePatch);

        $videos = new Videos($mockedBrowser);
        /** @var Video $result */
        $result = $videos->create($video['title']);

        $resultPath = $videos->updateThumbnailWithTimeCode($result->videoId, '00:00:12.4');
        $this->assertSame($videoPatch['assets']['thumbnail'], $resultPath->assets['thumbnail']);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Timecode is an empty file.
     */
    public function updateThumbnailWithEmptyTimeCodeFailed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videos = new Videos($mockedBrowser);
        $videos->updateThumbnailWithTimeCode('vi55mglWKqgywdX8Yu8WgDZ0', null);

    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function updateThumbnailWithBadTimeCodeFailed()
    {
        $return = '{
            "status": 400,
            "type": "https://docs.api.video/problems/attribute.invalid",
            "title": "The attribute is not a valid Libcast\\Shared\\ValueObject\\Timecode.",
            "name": "timecode"
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 400);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($return));

        $mockedBrowser = $this->getMockedOAuthBrowser();
        $mockedBrowser->method('patch')->willReturn($response);

        $videos = new Videos($mockedBrowser);
        $result = $videos->updateThumbnailWithTimeCode('vi55mglWKqgywdX8Yu8WgDZ0', 'salut');

        $this->assertNull($result);
        $error = $videos->getLastError();

        $this->assertSame(400, $error['status']);
        $return = json_decode($return, true);
        $this->assertSame($return, $error['message']);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function uploadSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": null,
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "updatedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
"metadata": [],
            "public": true,
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
            },
            "panoramic": true,
            "mp4Support": false
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));

        $video = json_decode($videoReturn, true);
        $mockedBrowser->method('post')->willReturn($response);
        $mockedBrowser->method('submit')->willReturn($response);

        $videos = new Videos($mockedBrowser);
        /** @var Video $result */
        $result = $videos->upload($this->getValideVideo()->url());
        $this->assertSame($video['videoId'], $result->videoId);
        $this->assertSame($video['title'], $result->title);
        $this->assertSame($video['description'], $result->description);
        $this->assertSame($video['publishedAt'], $result->publishedAt->format(\DATE_ATOM));
        $this->assertSame($video['tags'], $result->tags);
        $this->assertSame($video['metadata'], $result->metadata);
        $this->assertSame($video['source'], $result->source);
        $this->assertSame($video['assets'], $result->assets);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function uploadLargeFileSucceed()
    {
//        $this->markTestSkipped();
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": null,
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "updatedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
"metadata": [],
            "public": true,
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
            },
            "panoramic": true,
            "mp4Support": false
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));

        $video = json_decode($videoReturn, true);
        $mockedBrowser->method('post')->willReturn($response);
        $mockedBrowser->method('submit')->willReturn($response);

        $videos            = new Videos($mockedBrowser);
        $videos->chunkSize = 10 * 1024 * 1024;
        /** @var Video $result */
        $result = $videos->upload($this->getValideVideo()->url());
        $this->assertSame($video['videoId'], $result->videoId);
        $this->assertSame($video['title'], $result->title);
        $this->assertSame($video['description'], $result->description);
        $this->assertSame($video['publishedAt'], $result->publishedAt->format(\DATE_ATOM));
        $this->assertSame($video['tags'], $result->tags);
        $this->assertSame($video['metadata'], $result->metadata);
        $this->assertSame($video['source'], $result->source);
        $this->assertSame($video['assets'], $result->assets);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function uploadLargeFileWithBadVideoIdFailed()
    {
//        $this->markTestSkipped();
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 404);

        $mockedBrowser->method('submit')->willReturn($response);

        $videos            = new Videos($mockedBrowser);
        $videos->chunkSize = 10 * 1024 * 1024;
        $result = $videos->upload($this->getValideVideo()->url(), array(), 'vilWKqgywdX55mg8Yu8WgDZ0');

        $this->assertNull($result);
        $error = $videos->getLastError();

        $this->assertSame(404, $error['status']);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The source file must be readable.
     */
    public function uploadWithBadSourceShouldFail()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videos = new Videos($mockedBrowser);

        $videos->upload($this->filesystem->url().'/video/testfail.mp4');
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function uploadWithAlreadyVideoExistedShouldFail()
    {
        $return = '{
            "status": 400,
            "type": "https://docs.api.video/problems/file.already.uploaded",
            "title": "The source of the video is already uploaded.",
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

        $mockedBrowser->method('post')->willReturn($response);
        $mockedBrowser->method('submit')->willReturn($response);

        $videos = new Videos($mockedBrowser);

        $result = $videos->upload($this->getValideImage()->url(), array(), 'vi55mglWKqgywdX8Yu8WgDZ0');

        $this->assertNull($result);
        $error = $videos->getLastError();

        $this->assertSame(400, $error['status']);
        $return = json_decode($return, true);
        $this->assertSame($return, $error['message']);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage 'vfs://root/testempty.mp4' is an empty file.
     * @throws \ReflectionException
     */
    public function uploadWithEmptySourceShouldFail()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": null,
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "updatedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
"metadata": [],
            "public": true,
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
            },
            "panoramic": true,
            "mp4Support": false
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));


        $mockedBrowser->method('post')->willReturn($response);

        $videos = new Videos($mockedBrowser);

        $videos->upload($this->getInvalidVideo()->url());
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function uploadThumbnailSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": null,
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "updatedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
"metadata": [],
            "public": true,
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
            },
            "panoramic": true,
            "mp4Support": false
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));

        $video = json_decode($videoReturn, true);
        $mockedBrowser->method('submit')->willReturn($response);

        $videos = new Videos($mockedBrowser);
        /** @var Video $result */
        $result = $videos->uploadThumbnail($this->getValideThumbnail()->url(), $video['videoId']);
        $this->assertSame($video['videoId'], $result->videoId);
        $this->assertSame($video['title'], $result->title);
        $this->assertSame($video['description'], $result->description);
        $this->assertSame($video['publishedAt'], $result->publishedAt->format(\DATE_ATOM));
        $this->assertSame($video['tags'], $result->tags);
        $this->assertSame($video['metadata'], $result->metadata);
        $this->assertSame($video['source'], $result->source);
        $this->assertSame($video['assets'], $result->assets);
    }
    /**
     * @test
     * @throws \ReflectionException
     */
    public function uploadThumbnailPngFailed()
    {
        $return = '{
            "status": 400,
            "type": "https://docs.api.video/problems/file.extension",
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

        $videos = new Videos($mockedBrowser);
        /** @var Video $result */
        $result = $videos->uploadThumbnail($this->getInvalideImage()->url(), 'vi55mglWKqgywdX8Yu8WgDZ0');
        $this->assertNull($result);
        $error = $videos->getLastError();

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
        $videoReturn   = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": null,
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "updatedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
"metadata": [],
            "public": true,
            "panoramic": false,
            "mp4Support": false,
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
            }
        }';
        $video         = json_decode($videoReturn, true);
        $videos        = new Videos($mockedBrowser);

        $videos->uploadThumbnail($this->filesystem->url().'/image/testfail.jpg', $video['videoId']);
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

        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": null,
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "updatedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
"metadata": [],
            "public": true,
            "panoramic": false,
            "mp4Support": false,
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "iframe": "<iframe src=\'https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3\' width=\'100%\' height=\'100%\' frameborder=\'0\' scrolling=\'no\' allowfullscreen=\'\'></iframe>",
                "player": "https://embed.api.video/vi55mglWKqgywdX8Yu8WgDZ0?token=99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3",
                "hls": "https://cdn.api.video/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "thumbnail": "https://cdn.api.video/stream/99dc9d28-6de8-4c1e-adbe-d8e9a95ae2a3/thumbnail.jpg"
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));

        $video = json_decode($videoReturn, true);

        $mockedBrowser->method('post')->willReturn($response);

        $videos = new Videos($mockedBrowser);

        $videos->uploadThumbnail($this->getInvalidThumbnail()->url(), $video['videoId']);
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

        $videos = new Videos($mockedBrowser);

        $status = $videos->delete('vi55mglWKqgywdX8Yu8WgDZ0');
        $this->assertSame(204, $status);
    }



    /**
     * @test
     * @throws \ReflectionException
     */
    public function deleteWithBadVideoIdFailed()
    {
        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 404);

        $mockedBrowser = $this->getMockedOAuthBrowser();
        $mockedBrowser->method('delete')->willReturn($response);

        $videos = new Videos($mockedBrowser);
        $result = $videos->delete('vigywdX8Yu855mglWKqWgDZ0');

        $this->assertNull($result);
        $error = $videos->getLastError();

        $this->assertSame(404, $error['status']);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function downloadSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": null,
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "updatedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
"metadata": [],
            "public": true,
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8"
            },
            "panoramic": true,
            "mp4Support": false
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response,200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));

        $video = json_decode($videoReturn, true);
        $mockedBrowser->method('post')->willReturn($response);

        $videos = new Videos($mockedBrowser);
        /** @var Video $result */
        $result = $videos->download('http://path/to/video.mp4', $video['title']);
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
                    ->setMethods(array('get', 'submit', 'post', 'patch', 'delete'))
                    ->getMock();
    }

    private function getValideVideo()
    {

        return vfsStream::newFile('test.mp4')
                        ->withContent(LargeFileContent::withMegabytes(10))
                        ->at($this->filesystem);
    }

    private function getValideImage()
    {

        return vfsStream::newFile('test.jpg')
                        ->withContent(LargeFileContent::withMegabytes(2))
                        ->at($this->filesystem);
    }

    private function getInvalideImage()
    {

        return vfsStream::newFile('test.png')
                        ->withContent(LargeFileContent::withKilobytes(200))
                        ->at($this->filesystem);
    }

    private function getInvalidVideo()
    {

        return vfsStream::newFile('testempty.mp4')
                        ->withContent(LargeFileContent::withMegabytes(0))
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
