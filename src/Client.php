<?php

namespace ApiVideo\Client;

use Buzz\Client\Curl;
use Buzz\Client\FileGetContents;
use ApiVideo\Client\Api\Videos;
use ApiVideo\Client\Buzz\OAuthBrowser;

final class Client
{
    /** @var Videos */
    public $videos;

    /**
     *
     * @param string $username
     * @param string $password
     * @param string $domainName
     * @param string $baseUri
     */
    public function __construct($username, $password, $domainName = null, $baseUri = 'https://ws.api.video')
    {
        $client = extension_loaded('curl') ? new Curl : new FileGetContents;
        $browser = new OAuthBrowser($client);
        $browser->setBaseUri($baseUri);
        $browser->authenticate(
            $username,
            $password,
            $domainName
        );

        $this->videos = new Videos($browser);
    }
}
