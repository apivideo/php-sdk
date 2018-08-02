<?php

namespace ApiVideo\Client\Tests\Api;

use ApiVideo\Client\Api\Videos;
use ApiVideo\Client\Model\Video;
use Buzz\Message\Response;
use org\bovigo\vfs\content\LargeFileContent;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class VideosTest extends TestCase
{
    protected $filesystem;

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
     * @throws ReflectionException
     */
    public function getSucceed()
    {
        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "Test",
            "description": null,
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
            "metadata": [],
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
        $statusCode = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response,200);
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
     * @throws ReflectionException
     */
    public function searchSucceed()
    {
        $videoReturn = '
        {
            "data": [
                {
                    "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
                    "title": "Rosetta",
                    "description": null,
                    "publishedAt": "2018-05-03T17:21:11+02:00",
                    "tags": [],
                    "metadata": [],
                    "source": {
                        "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
                    },
                    "assets": {
                        "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8"
                    }
                },
                {
                    "videoId": "viABC",
                    "title": "Test ABC",
                    "description": "Description ABAC",
                    "publishedAt": "2018-05-03T17:21:12+02:00",
                    "tags": [
                        "a",
                        "b",
                        "c"
                    ],
                    "metadata": {
                        "baz": "wat",
                        "foo": "bar"
                    },
                    "source": {
                        "uri": "/videos/viABC/source"
                    },
                    "assets": {
                        "hls": "https://localhost/stream/33baeccf-587d-4407-97d0-78ea8ca7f8e2/hls/manifest.m3u8"
                    }
                },
                {
                    "videoId": "viDEF",
                    "title": "Test DEF",
                    "description": "Description DEF",
                    "publishedAt": "2018-05-03T17:21:13+02:00",
                    "tags": [
                        "c",
                        "d",
                        "e"
                    ],
                    "metadata": {
                        "baz": "wat",
                        "alice": "bob"
                    },
                    "source": {
                        "uri": "/videos/viDEF/source"
                    },
                    "assets": {
                        "hls": "https://localhost/stream/f1fc9970-c8b0-4cb0-8a3e-daffcfb6ff8d/hls/manifest.m3u8"
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
                        "uri": "http://localhost:8000/videos?pageSize=25&sortBy=title&sortOrder=asc"
                    },
                    {
                        "rel": "first",
                        "uri": "http://localhost:8000/videos?pageSize=25&sortBy=title&sortOrder=asc"
                    },
                    {
                        "rel": "last",
                        "uri": "http://localhost:8000/videos?pageSize=25&sortBy=title&sortOrder=asc"
                    }
                ]
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response,200);
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
     * @throws ReflectionException
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
                        "uri": "http://localhost:8000/videos?pageSize=25&sortBy=title&sortOrder=asc"
                    },
                    {
                        "rel": "last",
                        "uri": "http://localhost:8000/videos?pageSize=25&sortBy=title&sortOrder=asc"
                    }
                ]
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response,200);
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
     * @throws ReflectionException
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
                    "tags": [],
                    "metadata": [],
                    "source": {
                        "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
                    },
                    "assets": {
                        "hls": "https://localhost/stream/d69d7dad-143c-49d4-a585-2a0591ed6a56/hls/manifest.m3u8"
                    }
                },
                {
                    "videoId": "viABC",
                    "title": "Test ABC",
                    "description": "Description ABAC",
                    "publishedAt": "2018-05-03T17:21:12+02:00",
                    "tags": [
                        "a",
                        "b",
                        "c"
                    ],
                    "metadata": {
                        "baz": "wat",
                        "foo": "bar"
                    },
                    "source": {
                        "uri": "/videos/viABC/source"
                    },
                    "assets": {
                        "hls": "https://localhost/stream/c76c3856-7516-4b03-a94d-d76dce509a7d/hls/manifest.m3u8"
                    }
                },
                {
                    "videoId": "viDEF",
                    "title": "Test DEF",
                    "description": "Description DEF",
                    "publishedAt": "2018-05-03T17:21:13+02:00",
                    "tags": [
                        "c",
                        "d",
                        "e"
                    ],
                    "metadata": {
                        "baz": "wat",
                        "alice": "bob"
                    },
                    "source": {
                        "uri": "/videos/viDEF/source"
                    },
                    "assets": {
                        "hls": "https://localhost/stream/d28e4321-42dd-4dd3-b817-646ed0362f8e/hls/manifest.m3u8"
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
                        "uri": "http://localhost:8000/videos?pageSize=100&sortBy=title&sortOrder=asc"
                    },
                    {
                        "rel": "first",
                        "uri": "http://localhost:8000/videos?pageSize=100&sortBy=title&sortOrder=asc"
                    },
                    {
                        "rel": "last",
                        "uri": "http://localhost:8000/videos?pageSize=100&sortBy=title&sortOrder=asc"
                    }
                ]
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response,200);
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
     * @throws ReflectionException
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
            "tags": [],
            "metadata": [],
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8"
            }
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
     * @throws ReflectionException
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
     * @throws ReflectionException
     */
    public function updateSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": "",
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
            "metadata": [],
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8"
            }
        }';

        $videoPatch = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": "Description test",
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
            "metadata": [],
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "snapshot": "https://localhost/stream/64b3c497-c576-4b90-8d97-9c39fdbf727d/snapshot.jpg"
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response,200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));

        $responsePatch = new Response();

        $responsePatchReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCodePatch = $responsePatchReflected->getProperty('statusCode');
        $statusCodePatch->setAccessible(true);
        $statusCodePatch->setValue($responsePatch,200);
        $setContentPatch = $responsePatchReflected->getMethod('setContent');
        $setContentPatch->invokeArgs($responsePatch, array($videoPatch));

        $video = json_decode($videoReturn, true);

        $mockedBrowser->method('post')->willReturn($response);
        $mockedBrowser->method('patch')->willReturn($responsePatch);

        $videos = new Videos($mockedBrowser);
        /** @var Video $result */
        $result = $videos->create($video['title']);
        $this->assertEmpty($result->description);
        $properties =  array('description' => 'Description test');
        $resultPath = $videos->update($result->videoId, $properties);
        $this->assertSame('Description test', $resultPath->description);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function updateThumbnailWithTimeCodeSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": "",
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
            "metadata": [],
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8"
            }
        }';

        $videoPatch = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": "Description test",
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
            "metadata": [],
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "snapshot": "https://localhost/stream/64b3c497-c576-4b90-8d97-9c39fdbf727d/snapshot.jpg"
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response,200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));

        $responsePatch = new Response();

        $responsePatchReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCodePatch = $responsePatchReflected->getProperty('statusCode');
        $statusCodePatch->setAccessible(true);
        $statusCodePatch->setValue($responsePatch,200);
        $setContentPatch = $responsePatchReflected->getMethod('setContent');
        $setContentPatch->invokeArgs($responsePatch, array($videoPatch));

        $video = json_decode($videoReturn, true);
        $videoPatch = json_decode($videoPatch, true);

        $mockedBrowser->method('post')->willReturn($response);
        $mockedBrowser->method('patch')->willReturn($responsePatch);

        $videos = new Videos($mockedBrowser);
        /** @var Video $result */
        $result = $videos->create($video['title']);

        $resultPath = $videos->updateThumbnailWithTimeCode($result->videoId, '00:00:12.4');
        $this->assertSame($videoPatch['assets']['snapshot'], $resultPath->assets['snapshot']);
    }

    /**
     * @test
     * @throws ReflectionException
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
            "tags": [],
            "metadata": [],
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8"
            }
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
     * @throws ReflectionException
     */
    public function uploadLargeFileSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": null,
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
            "metadata": [],
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8"
            }
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
        $mockedBrowser->method('submit')->willReturn($response);

        $videos = new Videos($mockedBrowser);
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
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage 'vfs://root/video/testfail.mp4' must be a readable source file.
     */
    public function uploadWithBadSourceShouldFail()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $videos = new Videos($mockedBrowser);

        $videos->upload($this->filesystem->url().'/video/testfail.mp4');
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage 'vfs://root/testempty.mp4' is empty.
     * @throws ReflectionException
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
            "tags": [],
            "metadata": [],
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8"
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response,200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($videoReturn));


        $mockedBrowser->method('post')->willReturn($response);

        $videos = new Videos($mockedBrowser);

        $videos->upload($this->getInvalidVideo()->url());
    }

    /**
     * @test
     * @throws ReflectionException
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
            "tags": [],
            "metadata": [],
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "snapshot": "https://localhost/stream/64b3c497-c576-4b90-8d97-9c39fdbf727d/snapshot.jpg"
            }
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response,200);
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
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage 'vfs://root/image/testfail.jpg' must be a readable source file.
     */
    public function uploadThumbnailWithBadSourceShouldFail()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();
        $videoReturn = '
        {
            "videoId": "vi55mglWKqgywdX8Yu8WgDZ0",
            "title": "test.mp4",
            "description": null,
            "publishedAt": "2018-05-18T17:21:11+02:00",
            "tags": [],
            "metadata": [],
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8",
                "snapshot": "https://localhost/stream/64b3c497-c576-4b90-8d97-9c39fdbf727d/snapshot.jpg"
            }
        }';
        $video = json_decode($videoReturn, true);
        $videos = new Videos($mockedBrowser);

        $videos->uploadThumbnail($this->filesystem->url().'/image/testfail.jpg', $video['videoId']);
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage 'vfs://root/testempty.jpg' is empty.
     * @throws ReflectionException
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
            "tags": [],
            "metadata": [],
            "source": {
                "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/source"
            },
            "assets": {
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8"
            }
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

        $videos->uploadThumbnail($this->getInvalidThumbnail()->url(), $video['videoId']);
    }

    /**
     * @test
     * @throws ReflectionException
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

    private function getMockedOAuthBrowser()
    {
        return $this->getMockBuilder('ApiVideo\Client\Buzz\OAuthBrowser')
                    ->setMethods(array('get', 'submit', 'post', 'patch', 'delete'))
                    ->getMock();
    }

    private function getValideVideo()
    {

        return vfsStream::newFile('test.mp4')
                        ->withContent(LargeFileContent::withMegabytes(30))
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
