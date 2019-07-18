<?php


namespace ApiVideo\Client\Model\Analytic;


class AnalyticSessionEvent
{
    /** @var AnalyticSession */
    public $session;

    /** @var AnalyticResource */
    public $resource;

    /** @var AnalyticEvent[] */
    public $events;

    public function __construct()
    {
        $this->session  = new AnalyticSession();
        unset($this->session->metadata);
        $this->resource = new AnalyticResource();
        $this->events   = array();
    }
}
