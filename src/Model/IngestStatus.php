<?php

namespace ApiVideo\Client\Model;

class IngestStatus
{
    /** @var string */
    public $status;
    /** @var int */
    public $fileSize;
    /** @var array */
    public $receivedBytes;

    /**
     * IngestStatus constructor.
     * @param string $status
     * @param int    $fileSize
     * @param array  $receivedBytes
     */
    public function __construct($status, $fileSize, array $receivedBytes)
    {
        $this->status        = $status;
        $this->fileSize      = $fileSize;
        $this->receivedBytes = $receivedBytes;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data)
    {
        return new self(
            $data['status'],
            $data['filesize'],
            $data['receivedBytes']
        );
    }
}