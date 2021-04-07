<?php

namespace ApiVideo\Client\Model;

final class VideoStatus
{
    /** @var IngestStatus|null */
    public $ingest;

    /** @var EncodingStatus */
    public $encoding;

    /**
     * VideoStatus constructor.
     * @param IngestStatus|null   $ingest
     * @param EncodingStatus $encoding
     */
    public function __construct(IngestStatus $ingest = null, EncodingStatus $encoding)
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
            (array() === $data['ingest']) ? null : IngestStatus::fromArray($data['ingest']),
            EncodingStatus::fromArray($data['encoding'])
        );
    }
}
