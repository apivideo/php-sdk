<?php

namespace ApiVideo\Client;

use ApiVideo\Client\Api\Account;
use ApiVideo\Client\Api\AnalyticsLive;
use ApiVideo\Client\Api\AnalyticsSessionEvents;
use ApiVideo\Client\Api\AnalyticsVideo;
use ApiVideo\Client\Api\Captions;
use ApiVideo\Client\Api\Chapters;
use ApiVideo\Client\Api\Lives;
use ApiVideo\Client\Api\Players;
use ApiVideo\Client\Api\Tokens;
use ApiVideo\Client\Api\Videos;
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

    /** @var Chapters */
    public $chapters;

    /** @var Tokens */
    public $tokens;

    /** @var AnalyticsVideo */
    public $analyticsVideo;

    /** @var AnalyticsLive */
    public $analyticsLive;

    /** @var AnalyticsSessionEvents */
    public $analyticsSessionEvents;

    /** @var Account */
    public $account;

    /**
     * Create client for production environment.
     * @param string $apiKey
     * @return Client
     */
    public static function create($apiKey)
    {
        return new Client($apiKey, 'https://ws.api.video');
    }

    /**
     * Create client for sandbox environment.
     * @param string $apiKey
     * @return Client
     */
    public static function createSandbox($apiKey)
    {
        return new Client($apiKey, 'https://sandbox.api.video');
    }

    /**
     * @param string $apiKey
     * @param string $baseUri
     * @deprecated Use Client::create() or Client::createSandbox() instead
     */
    public function __construct($apiKey, $baseUri = 'https://ws.api.video')
    {
        $client = extension_loaded('curl') ? new Curl : new FileGetContents;
        $browser = new OAuthBrowser($client);
        $browser->setBaseUri($baseUri);
        $browser->authenticate($apiKey);

        $this->videos = new Videos($browser);
        $this->lives = new Lives($browser);
        $this->players = new Players($browser);
        $this->captions = new Captions($browser);
        $this->chapters = new Chapters($browser);
        $this->analyticsVideo = new AnalyticsVideo($browser);
        $this->analyticsLive = new AnalyticsLive($browser);
        $this->analyticsSessionEvents = new AnalyticsSessionEvents($browser);
        $this->tokens = new Tokens($browser);
        $this->account = new Account($browser);
    }
}
