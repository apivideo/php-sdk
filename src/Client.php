<?php

namespace ApiVideo\Client;

use ApiVideo\Client\Api\Account;
use ApiVideo\Client\Api\AnalyticsLive;
use ApiVideo\Client\Api\AnalyticsVideo;
use ApiVideo\Client\Api\Captions;
use ApiVideo\Client\Api\Lives;
use ApiVideo\Client\Api\Players;
use ApiVideo\Client\Api\Videos;
use ApiVideo\Client\Api\Tokens;
use ApiVideo\Client\Buzz\OAuthBrowser;
use Buzz\Client\Curl;
use Buzz\Client\FileGetContents;

final class Client
{
    /** @var Videos */
    public $videos;

    /** @var Lives */
    public $lives;

    /** @var Players */
    public $players;

    /** @var Captions */
    public $captions;

    /** @var Tokens */
    public $tokens;

    /** @var AnalyticsVideo */
    public $analyticsVideo;

    /** @var AnalyticsLive */
    public $analyticsLive;

    /** @var Account */
    public $account;

    /**
     *
     * @param $apiKey
     * @param string $baseUri
     */
    public function __construct($apiKey, $baseUri = 'https://ws.api.video')
    {
        $client  = extension_loaded('curl') ? new Curl : new FileGetContents;
        $browser = new OAuthBrowser($client);
        $browser->setBaseUri($baseUri);
        $browser->authenticate($apiKey);

        $this->videos  = new Videos($browser);
        $this->lives  = new Lives($browser);
        $this->players = new Players($browser);
        $this->captions = new Captions($browser);
        $this->analyticsVideo = new AnalyticsVideo($browser);
        $this->analyticsLive = new AnalyticsLive($browser);
        $this->tokens  = new Tokens($browser);
        $this->account  = new Account($browser);
    }
}
