<?php


use ApiVideo\Client\Buzz\FormByteRangeUpload;
use ApiVideo\Client\Exception\FileNotFoundException;
use org\bovigo\vfs\content\LargeFileContent;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class FormByteRangeUploadTest extends TestCase
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
     * @expectedException RuntimeException
     * @expectedExceptionMessage Missing file
     * @throws Exception
     */
    public function getHeadersWithMissingFileShouldFailed()
    {
        $form = new FormByteRangeUpload('',0, 2048);
        $form->getHeaders();
    }

    /**
     * @test
     * @throws Exception
     */
    public function getHeadersSucceed()
    {
        $form = new FormByteRangeUpload($this->getValideVideo()->url(),0, 2048, 10000000);
        $headers = $form->getHeaders();
        $this->assertContains('Content-Range: 0-2048/10000000', $headers);
    }

    private function getValideVideo()
    {

        return vfsStream::newFile('test.mp4')
                        ->withContent(LargeFileContent::withMegabytes(10))
                        ->at($this->filesystem);
    }
}
