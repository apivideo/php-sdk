<?php

namespace ApiVideo\Client\Tests\Api;

use PHPUnit\Framework\TestCase;
use Buzz\Message\Response;
use ApiVideo\Client\Api\Tokens;
use ReflectionClass;

final class TokensTest extends TestCase
{
    /**
     * @test
     * @throws \ReflectionException
     */
    public function generateSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();
        $response = new Response;
        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 201);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array(json_encode(array('token' => 'xyz'))));
        $mockedBrowser->method('post')->willReturn($response);

        $tokens = new Tokens($mockedBrowser);
        $token = $tokens->generate();

        $this->assertEquals('xyz', $token);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function getFailed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();
        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 500);

        $mockedBrowser->method('post')->willReturn($response);

        $tokens = new Tokens($mockedBrowser);
        $token = $tokens->generate();

        $this->assertNull($token);
        $error = $tokens->getLastError();

        $this->assertSame(500, $error['status']);
        $this->assertEmpty($error['message']);

    }

    private function getMockedOAuthBrowser()
    {
        return $this->getMockBuilder('ApiVideo\Client\Buzz\OAuthBrowser')
                    ->setMethods(array('get', 'submit', 'post', 'patch', 'delete'))
                    ->getMock();
    }
}
