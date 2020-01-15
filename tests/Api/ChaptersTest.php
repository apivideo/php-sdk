<?php

namespace ApiVideo\Client\Tests\Api;

use ApiVideo\Client\Api\Chapters;
use ApiVideo\Client\Model\Chapter;
use Buzz\Message\Response;
use org\bovigo\vfs\content\LargeFileContent;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use InvalidArgumentException;

class ChaptersTest extends TestCase
{
    protected $filesystem;

    protected function setUp()
    {
        parent::setUp();
        $directory = array(
            'video'   => array(),
            'chapter' => array(),
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
        $chapterReturn = '
        {
            "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/chapters/en",
            "src": "https://cdn-staging.api.video/vod/vi55mglWKqgywdX8Yu8WgDZ0/chapters/en.vtt",
            "language": "en"
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($chapterReturn));


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $chapters = new Chapters($oAuthBrowser);
        $chapter  = $chapters->get('vi55mglWKqgywdX8Yu8WgDZ0', 'en');

        $this->assertInstanceOf('ApiVideo\Client\Model\Chapter', $chapter);
        $this->assertSame('/videos/vi55mglWKqgywdX8Yu8WgDZ0/chapters/en', $chapter->uri);
        $this->assertSame(
            'https://cdn-staging.api.video/vod/vi55mglWKqgywdX8Yu8WgDZ0/chapters/en.vtt',
            $chapter->src
        );
        $this->assertSame('en', $chapter->language);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getWithBadVideoIdFailed()
    {
        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 404);


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $chapters = new Chapters($oAuthBrowser);
        $chapter  = $chapters->get('vilWKqgy55mgwdX8Yu8WgDZ0', 'en');

        $this->assertNull($chapter);
        $error = $chapters->getLastError();

        $this->assertSame(404, $error['status']);
        $this->assertEmpty($error['message']);

    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getAll()
    {
        $chapterReturn = array(
            'data'       => array(
                array(
                    'uri'     => '/videos/vi55mglWKqgywdX8Yu8WgDZ0/chapters/en',
                    'src'     => 'https://cdn-staging.api.video/vod/vi55mglWKqgywdX8Yu8WgDZ0/chapters/en.vtt',
                    'language' => 'en'
                ),
            ),
            'pagination' => '',
        );

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array(json_encode($chapterReturn)));


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $chapters    = new Chapters($oAuthBrowser);
        $chapterList = $chapters->getAll('vi55mglWKqgywdX8Yu8WgDZ0');
        $chapter     = current($chapterList);
        $this->assertInstanceOf('ApiVideo\Client\Model\Chapter', $chapter);
        $this->assertSame('/videos/vi55mglWKqgywdX8Yu8WgDZ0/chapters/en', $chapter->uri);
        $this->assertSame(
            'https://cdn-staging.api.video/vod/vi55mglWKqgywdX8Yu8WgDZ0/chapters/en.vtt',
            $chapter->src
        );
        $this->assertSame('en', $chapter->language);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getAllWithBadVideoIdFailed()
    {
        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 404);


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $chapters = new Chapters($oAuthBrowser);
        $chapter  = $chapters->getAll('vilWKqgy55mgwdX8Yu8WgDZ0');

        $this->assertNull($chapter);
        $error = $chapters->getLastError();

        $this->assertSame(404, $error['status']);
        $this->assertEmpty($error['message']);

    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function uploadSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $chapterReturn = '
        {
            "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/chapters/en",
            "src": "https://cdn-staging.api.video/vod/vi55mglWKqgywdX8Yu8WgDZ0/chapters/en.vtt",
            "language": "en"
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($chapterReturn));

        $chapter = json_decode($chapterReturn, true);

        $mockedBrowser->method('post')->willReturn($response);
        $mockedBrowser->method('submit')->willReturn($response);

        $chapters = new Chapters($mockedBrowser);

        /** @var Chapter $result */
        $result = $chapters->upload($this->getValideChapter()->url(),
            array('videoId' => 'vi55mglWKqgywdX8Yu8WgDZ0', 'language' => 'en'));
        $this->assertSame($chapter['uri'], $result->uri);
        $this->assertSame($chapter['src'], $result->src);
        $this->assertSame($chapter['language'], $result->language);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The source file must be readable.
     */
    public function uploadWithBadSourceShouldFail()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $chapters = new Chapters($mockedBrowser);

        $chapters->upload($this->filesystem->url().'/chapters/test.vtt');
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function uploadWithBadVideoIdShouldFail()
    {

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 404);

        $mockedBrowser = $this->getMockedOAuthBrowser();

        $mockedBrowser->method('submit')->willReturn($response);

        $chapters = new Chapters($mockedBrowser);

        $result = $chapters->upload($this->getValideChapter()->url(),
            array('videoId' => 'viglWK55mqgywdX8Yu8WgDZ0', 'language' => 'en'));

        $this->assertNull($result);
        $error = $chapters->getLastError();

        $this->assertSame(404, $error['status']);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage "videoId" property must be set for upload chapter.
     */
    public function uploadWithMissingVideoIdShouldFail()
    {

        $mockedBrowser = $this->getMockedOAuthBrowser();

        $chapters = new Chapters($mockedBrowser);

        $chapters->upload($this->getValideChapter()->url(), array('language' => 'en'));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage "language" property must be set for upload chapter.
     */
    public function uploadWithMissingLanguageShouldFail()
    {

        $mockedBrowser = $this->getMockedOAuthBrowser();

        $chapters = new Chapters($mockedBrowser);

        $chapters->upload($this->getValideChapter()->url(), array('videoId' => 'viglWK55mqgywdX8Yu8WgDZ0'));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage 'vfs://root/testempty.vtt' is an empty file.
     */
    public function uploadWithEmptySourceShouldFail()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $chapters = new Chapters($mockedBrowser);

        $chapters->upload($this->getInvalidChapter()->url(),
            array('videoId' => 'viglWK55mqgywdX8Yu8WgDZ0', 'language' => 'en'));
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

        $chapters = new Chapters($mockedBrowser);

        $status = $chapters->delete('vi55mglWKqgywdX8Yu8WgDZ0', 'en');
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

        $chapters = new Chapters($mockedBrowser);
        $result   = $chapters->delete('vi5Kqg5mgywdX8Yu8WgDZ0', 'en');

        $this->assertNull($result);
        $error = $chapters->getLastError();

        $this->assertSame(404, $error['status']);
    }

    private function getMockedOAuthBrowser()
    {
        return $this->getMockBuilder('ApiVideo\Client\Buzz\OAuthBrowser')
            ->setMethods(array('get', 'submit', 'post', 'patch', 'delete'))
            ->getMock();
    }

    private function getValideChapter()
    {

        return vfsStream::newFile('chapter.vtt')
            ->withContent(LargeFileContent::withKilobytes(2))
            ->at($this->filesystem);
    }

    private function getInvalidChapter()
    {

        return vfsStream::newFile('testempty.vtt')
            ->withContent(LargeFileContent::withKilobytes(0))
            ->at($this->filesystem);
    }
}
