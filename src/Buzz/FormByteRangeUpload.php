<?php

namespace ApiVideo\Client\Buzz;

use Buzz\Message\Form\FormUpload;

class FormByteRangeUpload extends FormUpload
{
    /**
     *
     * @var int
     */
    private $from;

    /**
     *
     * @var int
     */
    private $to;

    /**
     *
     * @var int
     */
    private $length;

    /**
     *
     * @param string $file
     * @param int $from
     * @param int $to
     * @param int $length
     * @param string $contentType
     */
    public function __construct($file, $from, $to, $length = null, $contentType = null)
    {
        parent::__construct($file, $contentType);

        $this->from = $from;
        $this->to = $to;
        $this->length = $length;
    }

    /**
     *
     * @return array
     * @throws \Exception
     */
    public function getHeaders()
    {
        $headers = parent::getHeaders();

        if (!$file = $this->getFile()) {
            throw new \Exception('Missing file');
        }

        $headers[] = sprintf('Content-Range: %d-%d/%d',
            $this->from,
            $this->to,
            $this->length);

        return $headers;
    }
}
