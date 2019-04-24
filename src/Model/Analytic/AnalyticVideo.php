<?php

namespace ApiVideo\Client\Model\Analytic;

class AnalyticVideo
{
    /** @var string */
    public $videoId;

    /** @var string */
    public $videoTitle;

    /** @var array */
    public $metadata = array();

    /** @var string */
    public $period;

    /** @var AnalyticData */
    public $data;
}
