<?php


use ApiVideo\Client\Buzz\OAuthBrowser;
use PHPUnit\Framework\TestCase;

class OAuthBrowserTest extends TestCase
{
    /**
     * @test
     * @throws ReflectionException
     */
    public function setBaseUriSucceed()
    {
        $browser = new OAuthBrowser();

        $browserReflected = new ReflectionClass('ApiVideo\Client\Buzz\OAuthBrowser');
        $baseUri = $browserReflected->getProperty('baseUri');
        $baseUri->setAccessible(true);

        $setbaseUri = $browserReflected->getMethod('setBaseUri');

        $setbaseUri->invokeArgs($browser, array('https://api.video'));

        $this->assertSame('https://api.video', $baseUri->getValue($browser));


    }
}
