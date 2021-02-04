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

    /**
     * @test
     */
    public function userAgentHeaderCreationWithApplicationNameSucceed()
    {
        $browser = new OAuthBrowser(null, null, "go.api.video");

        $browserReflected = new ReflectionClass('ApiVideo\Client\Buzz\OAuthBrowser');

        $headersProperty = $browserReflected->getProperty("headers");
        $headersProperty->setAccessible(true);
        $headers = $headersProperty->getValue($browser);

        $this->assertSame("api.video SDK (php; v:" . OAuthBrowser::SDK_VERSION . "; go.api.video)", $headers["User-Agent"]);
    }

    /**
     * @test
     */
    public function userAgentHeaderCreationWithoutApplicationNameSucceed()
    {
        $browser = new OAuthBrowser();

        $browserReflected = new ReflectionClass('ApiVideo\Client\Buzz\OAuthBrowser');

        $headersProperty = $browserReflected->getProperty("headers");
        $headersProperty->setAccessible(true);
        $headers = $headersProperty->getValue($browser);

        $this->assertSame("api.video SDK (php; v:" . OAuthBrowser::SDK_VERSION . "; )", $headers["User-Agent"]);
    }
}
