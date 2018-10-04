<?php

namespace ApiVideo\Client\Model;

final class Live
{
    /** @var string */
    public $liveStreamId;

    /** @var string */
    public $name;

    /** @var boolean */
    public $record;

    /** @var string */
    public $streamKey;

    /** @var array */
    public $assets = array();
}
