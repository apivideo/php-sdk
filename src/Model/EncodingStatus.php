<?php

namespace ApiVideo\Client\Model;

class EncodingStatus
{
    /** @var boolean */
    public $playable;
    /** @var array */
    public $qualities;
    /** @var array */
    public $metadata;

    /**
     * VideoEncoding constructor.
     * @param bool  $playable
     * @param array $qualities
     * @param array $metadata
     */
    public function __construct($playable, array $qualities, array $metadata)
    {
        $this->playable  = $playable;
        $this->qualities = $qualities;
        $this->metadata  = $metadata;
    }

    /**
     * @param array $data
     * @return EncodingStatus
     */
    public static function fromArray(array $data)
    {
        return new self(
            $data['playable'],
            $data['qualities'],
            $data['metadata']
        );
    }
}