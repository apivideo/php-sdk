<?php

namespace ApiVideo\Client\Tests\Api;

use PHPUnit\Framework\TestCase;
use Buzz\Message\Response;
use ApiVideo\Client\Api\Tokens;

final class TokensTest extends TestCase
{
    public function testGenerate()
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

    private function getMockedOAuthBrowser()
    {
        return $this->getMockBuilder('ApiVideo\Client\Buzz\OAuthBrowser')
                    ->setMethods(array('get', 'submit', 'post', 'patch', 'delete'))
                    ->getMock();
    }
}
