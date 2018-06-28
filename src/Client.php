<?php

namespace ApiVideo\Client;

use ApiVideo\Client\Api\Players;
use ApiVideo\Client\Api\Videos;
use ApiVideo\Client\Buzz\OAuthBrowser;
use Buzz\Client\Curl;
use Buzz\Client\FileGetContents;

final class Client
{
    /** @var Videos */
    public $videos;

    /** @var Players */
    public $players;

    /**
     *
     * @param string $username
     * @param string $password
     * @param string $domainName
     * @param string $baseUri
     */
    public function __construct($username, $password, $domainName = null, $baseUri = 'https://ws.api.video')
    {
        $client  = extension_loaded('curl') ? new Curl : new FileGetContents;
        $browser = new OAuthBrowser($client);
        $browser->setBaseUri($baseUri);
        $browser->authenticate(
            $username,
            $password,
            $domainName
        );

        $this->videos  = new Videos($browser);
        $this->players = new Players($browser);
    }
}
