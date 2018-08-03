<?php


namespace ApiVideo\Client\Model\Analytic;


class AnalyticSession
{
    /** @var string */
    public $sessionId;

    /** @var \DateTimeInterface */
    public $loadedAt;

    /** @var \DateTimeInterface */
    public $endedAt;
}
