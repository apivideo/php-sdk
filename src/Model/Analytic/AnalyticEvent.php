<?php


namespace ApiVideo\Client\Model\Analytic;


class AnalyticEvent
{
    /** @var string */
    public $type;

    /** @var \DateTimeInterface */
    public $emittedAt;

    /** @var int|null */
    public $at;

    /** @var int|null */
    public $from;

    /** @var int|null */
    public $to;
}
