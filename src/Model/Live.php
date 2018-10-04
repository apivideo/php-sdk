<?php

namespace ApiVideo\Client\Model;

final class Live
{
    /** @var string */
    public $liveStreamId;

    /** @var string */
    public $name;

    public $record;

    public $streamKey;

    /** @var array */
    public $assets = array();
}
