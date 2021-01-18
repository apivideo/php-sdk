<?php

namespace ApiVideo\Client\Buzz;

use ApiVideo\Client\Exception\AuthenticationFailed;
use Buzz\Browser;
use Buzz\Client\ClientInterface;
use Buzz\Message\Factory\FactoryInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Message\Response;

class OAuthBrowser extends Browser
{
    /** @var array */
    private $authPayload = array();

    /** @var bool */
    private $isAuthenticated = false;

    /** @var array */
    private $headers;

    /** @var string */
    private $baseUri;

    public function __construct(ClientInterface $client = null, FactoryInterface $factory = null)
    {
        parent::__construct($client, $factory);

        $composerJson = file_get_contents(__DIR__ . "/../../composer.json");

        $packageVersion = json_decode($composerJson)->version;

        $this->headers = array(
            'User-Agent' => 'api.video SDK (php; v:'.$packageVersion.')'
        );
    }
    /**
     *
     * @param string $baseUri
     */
    public function setBaseUri($baseUri)
    {
        $this->baseUri = $baseUri;
    }

    /**
     *
     * @param $apiKey
     */
    public function authenticate($apiKey)
    {
        $this->authPayload = array(
            'apiKey' => $apiKey,
        );

        $this->getAccessToken();
    }

    /**
     *
     * @throws AuthenticationFailed
     */
    private function getAccessToken()
    {
        if (empty($this->authPayload)) {
            throw new AuthenticationFailed('Authentication failed');
        }

        /* @var $response Response */
        $response =
            parent::post(
                $this->baseUri.'/auth/api-key',
                array(),
                json_encode($this->authPayload)
            );

        if ($response->getStatusCode() >= 400) {
            throw new AuthenticationFailed('Authentication failed');
        }

        $this->headers['Authorization'] = 'Bearer '.json_decode($response->getContent())->access_token;
        $this->isAuthenticated          = true;
    }

    /**
     *
     * @param Response|MessageInterface $response
     * @return Response
     */
    private function check(Response $response)
    {
        $status = $response->getStatusCode();

        // Refresh access token automatically and transparently
        if (
            401 === $status &&
            array('application/problem+json') === $response->getHeader('Content-Type', false) &&
            false !== strpos($response->getContent(), 'access_denied')
        ) {
            $lastRequest = $this->getLastRequest();

            // Refresh access token
            $this->getAccessToken();

            // Re issue the request
            $headers = $lastRequest->getHeaders();
            foreach ($headers as $key => $header) {
                if (0 === stripos($header, 'Authorization:')) {
                    unset($headers[$key]);
                }
            }
            $headers[] = 'Authorization: '.$this->headers['Authorization'];
            $lastRequest->setHeaders($headers);
            /** @var Response $response */
            $response = $this->send($lastRequest);
        }

        return $response;
    }

    /**
     *
     * @param string $path
     * @param array $headers
     * @return Response
     */
    public function get($path, $headers = array())
    {
        return $this->check(
            parent::get(
                $this->baseUri.$path,
                array_merge($this->headers, $headers)
            )
        );
    }

    /**
     *
     * @param string $path
     * @param array $headers
     * @param string $content
     * @return Response
     */
    public function post($path, $headers = array(), $content = '')
    {
        return $this->check(
            parent::post(
                $this->baseUri.$path,
                array_merge($this->headers, $headers),
                $content
            )
        );
    }

    /**
     * Submit a form (upload file for example)
     *
     * @param string $path
     * @param array $fields
     * @param string $method
     * @param array $headers
     * @return Response
     */
    public function submit($path, array $fields = array(), $method = RequestInterface::METHOD_POST, $headers = array())
    {
        return $this->check(
            parent::submit(
                $this->baseUri.$path,
                $fields,
                $method,
                array_merge($this->headers, $headers)
            )
        );
    }

    /**
     *
     * @param string $path
     * @param array $headers
     * @param string $content
     * @return Response
     */
    public function put($path, $headers = array(), $content = '')
    {
        return $this->check(
            parent::put(
                $this->baseUri.$path,
                array_merge($this->headers, $headers),
                $content
            )
        );
    }

    /**
     *
     * @param string $path
     * @param array $headers
     * @param string $content
     * @return Response
     */
    public function patch($path, $headers = array(), $content = '')
    {
        return $this->check(
            parent::patch(
                $this->baseUri.$path,
                array_merge($this->headers, $headers),
                $content
            )
        );
    }

    /**
     *
     * @param string $path
     * @param array $headers
     * @param string $content
     * @return Response
     */
    public function delete($path, $headers = array(), $content = '')
    {
        return $this->check(
            parent::delete(
                $this->baseUri.$path,
                array_merge($this->headers, $headers),
                $content
            )
        );
    }

    /**
     *
     * @param string $path
     * @param array $headers
     * @return Response
     */
    public function head($path, $headers = array())
    {
        return $this->check(
            parent::head(
                $this->baseUri.$path,
                array_merge($this->headers, $headers)
            )
        );
    }

    /**
     *
     * @param string $path
     * @param array $headers
     * @return Response
     */
    public function options($path, array $headers = array())
    {
        return $this->check(
            $this->call(
                $this->baseUri.$path,
                'OPTIONS',
                array_merge($this->headers, $headers)
            )
        );
    }
}
