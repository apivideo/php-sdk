<?php


namespace ApiVideo\Client\Model\Analytic;


use DateTimeInterface;

class AnalyticSession
{
    /** @var string */
    public $sessionId;

    /** @var DateTimeInterface */
    public $loadedAt;

    /** @var DateTimeInterface */
    public $endedAt;

    /** @var array */
    public $metadata = array();
}
