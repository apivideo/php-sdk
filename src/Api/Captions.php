<?php

namespace ApiVideo\Client\Api;

use ApiVideo\Client\Model\Caption;
use Buzz\Message\Form\FormUpload;
use InvalidArgumentException;

class Captions extends BaseApi
{
    /**
     * @param string $videoId
     * @param $language
     * @return Caption|null
     */
    public function get($videoId, $language)
    {
        $response = $this->browser->get("/videos/$videoId/captions/$language");
        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }
        return $this->unmarshal($response);
    }

    /**
     * @param string $videoId
     * @return Caption[]|null
     */
    public function getAll($videoId)
    {
        $response = $this->browser->get("/videos/$videoId/captions");
        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        $json     = json_decode($response->getContent(), true);
        $captions = $json['data'];

        return $this->castAll($captions);
    }


    /**
     * @param $source
     * @param array $properties
     * @return Caption|null
     */
    public function upload($source, array $properties = array())
    {
        if (!is_readable($source)) {
            throw new InvalidArgumentException('The source file must be readable.');
        }

        if (!isset($properties['videoId'])) {
            throw new InvalidArgumentException('"videoId" property must be set for upload caption.');
        }

        if (!isset($properties['language'])) {
            throw new InvalidArgumentException('"language" property must be set for upload caption.');
        }
        $videoId  = $properties['videoId'];
        $language = $properties['language'];

        $resource = fopen($source, 'rb');

        $stats  = fstat($resource);
        $length = $stats['size'];
        if (0 >= $length) {
            throw new InvalidArgumentException("'$source' is an empty file.");
        }

        $response = $this->browser->submit(
            "/videos/$videoId/captions/$language",
            array('file' => new FormUpload($source))
        );

        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $this->unmarshal($response);
    }

    /**
     * @param string $videoId
     * @param $language
     * @param $isDefault
     * @return Caption
     */
    public function updateDefault($videoId, $language, $isDefault)
    {
        $response = $this->browser->patch(
            "/videos/$videoId/captions/$language",
            array(),
            json_encode(array('default' => $isDefault))
        );

        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $this->unmarshal($response);
    }

    /**
     * @param string $videoId
     * @param $language
     * @return int|null
     */
    public function delete($videoId, $language)
    {
        $response = $this->browser->delete("/videos/$videoId/captions/$language");

        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $response->getStatusCode();
    }

    /**
     * @param array $data
     * @return Caption
     */
    protected function cast(array $data)
    {
        $caption          = new Caption();
        $caption->uri     = $data['uri'];
        $caption->src     = $data['src'];
        $caption->srclang = $data['srclang'];
        $caption->default = $data['default'];

        return $caption;
    }
}
