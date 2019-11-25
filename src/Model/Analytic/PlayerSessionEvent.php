<?php


namespace ApiVideo\Client\Model\Analytic;


use DateTimeInterface;

class PlayerSessionEvent
{
    /** @var string */
    public $type;

    /** @var DateTimeInterface */
    public $emittedAt;

    /** @var int|null */
    public $at;

    /** @var int|null */
    public $from;

    /** @var int|null */
    public $to;

    /**
     * SessionEvent constructor.
     * @param string            $type
     * @param DateTimeInterface $emittedAt
     * @param int|null          $at
     * @param int|null          $from
     * @param int|null          $to
     */
    public function __construct($type, DateTimeInterface $emittedAt, $at, $from, $to)
    {
        $this->type      = $type;
        $this->emittedAt = $emittedAt;
        $this->at        = $at;
        $this->from      = $from;
        $this->to        = $to;
    }
}
