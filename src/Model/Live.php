<?php


namespace ApiVideo\Client\Model;


class Live
{
    /** @var string */
    public $liveStreamId;

    /** @var string */
    public $streamKey;

    /** @var string */
    public $name;

    /** @var boolean */
    public $record;

    /** @var boolean */
    public $broadcasting;

    /** @var boolean */
    public $public;

    /** @var array */
    public $assets = array();

    /** @var string */
    public $playerId;
}
