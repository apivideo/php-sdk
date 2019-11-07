<?php

namespace ApiVideo\Client\Model;

final class VideoStatus
{
    /** @var IngestStatus */
    public $ingest;

    /** @var EncodingStatus */
    public $encoding;

    /**
     * VideoStatus constructor.
     * @param IngestStatus   $ingest
     * @param EncodingStatus $encoding
     */
    public function __construct(IngestStatus $ingest, EncodingStatus $encoding)
    {
        $this->ingest   = $ingest;
        $this->encoding = $encoding;
    }

    /**
     * @param array $data
     * @return VideoStatus
     */
    public static function fromArray(array $data)
    {
        return new self(
            IngestStatus::fromArray($data['ingest']),
            EncodingStatus::fromArray($data['encoding'])
        );
    }
}
