<?php


use ApiVideo\Client\Api\Videos;
use ApiVideo\Client\Buzz\OAuthBrowser;
use ApiVideo\Client\Model\Video;
use Buzz\Message\Response;
use PHPUnit\Framework\TestCase;

class VideosTest extends TestCase
{
    /**
     * @test
     */
    public function getSucceed()
    {
        $videoId     = 'vi55mglWKqgywdX8Yu8WgDZ0';
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
                "hls": "https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8"
            }
        }';

        $response = new Response();
        $response->setContent($videoReturn);


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $videos = new Videos($oAuthBrowser);
        $video  = $videos->get($videoId);

        $this->assertInstanceOf(Video::class, $video);
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
            'https://localhost/stream/d441c757-a9c1-4f4c-ad79-280a707c2b77/hls/manifest.m3u8',
            $video->assets['hls']
        );

    }

    /**
     * @test
     * @expectedException TypeError
     */
    public function getWithFakeVideoIdShouldFail()
    {
        $videoId = 'vi55mglWKqgywdX8Yu8WgDZ0';


        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $videos = new Videos($oAuthBrowser);
        try{
            $videos->get($videoId);
        }catch (\Exception $e){

        }


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
        $response->setContent($videoReturn);

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $videos = new Videos($oAuthBrowser);
        $results = $videos->search(array(
            'currentPage' => 1,
            'pageSize' => 25,
            'sortBy' => 'title',
            'sortOrder' => 'asc'
        ));

        $videosReflected = new ReflectionClass(Videos::class);
        $castAll = $videosReflected->getMethod('castAll');
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
        $response->setContent($videoReturn);

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $videos = new Videos($oAuthBrowser);
        $results = $videos->search(array(
            'currentPage' => 1,
            'pageSize' => 25,
            'sortBy' => 'title',
            'sortOrder' => 'asc'
        ));

        $videosReflected = new ReflectionClass(Videos::class);
        $castAll = $videosReflected->getMethod('castAll');
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
        $response->setContent($videoReturn);

        $oAuthBrowser = $this->getMockedOAuthBrowser();

        $oAuthBrowser->method('get')->willReturn($response);

        $videos = new Videos($oAuthBrowser);
        $results = $videos->search(array(
            'currentPage' => 1,
            'pageSize' => 25,
            'sortBy' => 'title',
            'sortOrder' => 'asc'
        ));

        $videosReflected = new ReflectionClass(Videos::class);
        $castAll = $videosReflected->getMethod('castAll');
        $castAll->setAccessible(true);

        $videosReturn = json_decode($videoReturn, true);

        $this->assertEquals(array_merge(array(), $castAll->invokeArgs($videos, $videosReturn)), $results);



    }

    private function getMockedOAuthBrowser()
    {
        return $this->getMockBuilder(OAuthBrowser::class)
                    ->disableOriginalConstructor()
                    ->setMethods(['get'])
                    ->getMock();
    }
}
