<?php

namespace ApiVideo\Client\Api;

use ApiVideo\Client\Buzz\OAuthBrowser;
use ApiVideo\Client\Model\Account as AccountModel;
use ApiVideo\Client\Model\Analytic\PlayerSession;
use ApiVideo\Client\Model\Caption;
use ApiVideo\Client\Model\Chapter;
use ApiVideo\Client\Model\Live;
use ApiVideo\Client\Model\Player;
use ApiVideo\Client\Model\Video;
use Buzz\Message\MessageInterface;
use Buzz\Message\Response;

abstract class BaseApi
{
    /** @var array */
    private $lastError;

    /** @var OAuthBrowser */
    protected $browser;

    /**
     * @param OAuthBrowser $browser
     */
    public function __construct(OAuthBrowser $browser)
    {
        $this->browser = $browser;
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function getBrowser()
    {
        return $this->browser;
    }

    protected function registerLastError(Response $response)
    {
        $this->lastError = array(
            'status'  => $response->getStatusCode(),
            'message' => json_decode($response->getContent(), true),
        );
    }

    /**
     * @param MessageInterface $message
     * @return Caption|Chapter|Player|Video|Live|PlayerSession|AccountModel
     */
    protected function unmarshal(MessageInterface $message)
    {
        return $this->cast(json_decode($message->getContent(), true));
    }

    /**
     * @param array $collection
     * @return Caption[]|Chapter[]|Players[]|Video[]|Live[]|PlayerSession[]
     */
    protected function castAll(array $collection)
    {
        return array_map(array($this, 'cast'), $collection);
    }

    abstract protected function cast(array $data);
}
