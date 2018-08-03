<?php


namespace ApiVideo\Client\Api;


use ApiVideo\Client\Buzz\OAuthBrowser;
use ApiVideo\Client\Model\Analytic\Analytic;
use ApiVideo\Client\Model\Caption;
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

    protected function registerLastError(Response $response)
    {
        $this->lastError = array(
            'status'  => $response->getStatusCode(),
            'message' => json_decode($response->getContent(), true),
        );
    }

    /**
     * @param \Buzz\Message\MessageInterface $message
     * @return Caption|Player|Video|Analytic
     */
    protected function unmarshal(MessageInterface $message)
    {
        return $this->cast(json_decode($message->getContent(), true));
    }

    /**
     * @param array $collection
     * @return Caption[]|Players[]|Video[]|Analytic[]
     */
    protected function castAll(array $collection)
    {
        return array_map(array($this, 'cast'), $collection);
    }

    abstract protected function cast(array $data);
}
