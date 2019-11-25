<?php


namespace ApiVideo\Client\Model\Analytic;


class PlayerSession
{
    /** @var AnalyticSession */
    public $session;

    /** @var AnalyticLocation */
    public $location;

    /** @var AnalyticReferrer */
    public $referrer;

    /** @var AnalyticDevice */
    public $device;

    /** @var AnalyticOs */
    public $os;

    /** @var AnalyticClient */
    public $client;

    public function __construct()
    {
        $this->session  = new AnalyticSession();
        $this->location = new AnalyticLocation();
        $this->referrer = new AnalyticReferrer();
        $this->device   = new AnalyticDevice();
        $this->os       = new AnalyticOs();
        $this->client   = new AnalyticClient();
    }
}
