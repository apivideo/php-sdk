<?php

namespace ApiVideo\Client\Api;

use ApiVideo\Client\Model\Chapter;
use Buzz\Message\Form\FormUpload;
use InvalidArgumentException;

class Chapters extends BaseApi
{
    /**
     * @param string $videoId
     * @param $language
     * @return Chapter|null
     */
    public function get($videoId, $language)
    {
        $response = $this->browser->get("/videos/$videoId/chapters/$language");
        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $this->unmarshal($response);
    }

    /**
     * @param string $videoId
     * @return Chapter[]|null
     */
    public function getAll($videoId)
    {
        $response = $this->browser->get("/videos/$videoId/chapters");
        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        $json = json_decode($response->getContent(), true);
        $chapters = $json['data'];

        return $this->castAll($chapters);
    }


    /**
     * @param $source
     * @param array $properties
     * @return Chapter|null
     */
    public function upload($source, array $properties = array())
    {
        if (!is_readable($source)) {
            throw new InvalidArgumentException('The source file must be readable.');
        }

        if (!isset($properties['videoId'])) {
            throw new InvalidArgumentException('"videoId" property must be set for upload chapter.');
        }

        if (!isset($properties['language'])) {
            throw new InvalidArgumentException('"language" property must be set for upload chapter.');
        }
        $videoId = $properties['videoId'];
        $language = $properties['language'];

        $resource = fopen($source, 'rb');

        $stats = fstat($resource);
        $length = $stats['size'];
        if (0 >= $length) {
            throw new InvalidArgumentException("'$source' is an empty file.");
        }

        $response = $this->browser->submit(
            "/videos/$videoId/chapters/$language",
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
     * @return int|null
     */
    public function delete($videoId, $language)
    {
        $response = $this->browser->delete("/videos/$videoId/chapters/$language");

        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $response->getStatusCode();
    }

    /**
     * @param array $data
     * @return Chapter
     */
    protected function cast(array $data)
    {
        $chapter = new Chapter();
        $chapter->uri = $data['uri'];
        $chapter->src = $data['src'];
        $chapter->language = $data['language'];

        return $chapter;
    }
}
