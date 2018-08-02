<?php

namespace ApiVideo\Client\Api;

use ApiVideo\Client\Buzz\FormByteRangeUpload;
use ApiVideo\Client\Buzz\OAuthBrowser;
use ApiVideo\Client\Model\Video;
use Buzz\Message\Form\FormUpload;
use Buzz\Message\RequestInterface;

class Videos extends BaseApi
{
    /** @var int Upload chunk size in bytes */
    public $chunkSize; // 64 MiB;

    /** @var Captions */
    public $captions;

    public function __construct(OAuthBrowser $browser)
    {
        parent::__construct($browser);
        $this->chunkSize = 64 * 1024 * 1024;
    }

    /**
     * @param string $videoId
     * @return Video|null
     */
    public function get($videoId)
    {
        $response = $this->browser->get("/videos/$videoId");
        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $this->unmarshal($response);
    }

    /**
     * Incrementally iterate over a collection of elements.
     * By default the elements are returned in an array, unless you pass a
     * $callback which will be called for each instance of Video.
     * Available parameters:
     *   - currentPage (int)   current pagination page
     *   - pageSize    (int)   number of elements per page
     *   - videoIds    (array) videoIds to limit the search to
     *   - tags        (array)
     *   - metadata    (array)
     * If currentPage and pageSize are not given, the method iterates over all
     * pages of results and return an array containing all the results.
     *
     * @param array $parameters
     * @param callable $callback
     * @return Video[]|null
     */
    public function search(array $parameters = array(), $callback = null)
    {
        $params             = $parameters;
        $currentPage        = isset($parameters['currentPage']) ? $parameters['currentPage'] : 1;
        $params['pageSize'] = isset($parameters['pageSize']) ? $parameters['pageSize'] : 100;
        $allVideos          = array();

        do {
            $params['currentPage'] = $currentPage;
            $response              = $this->browser->get('/videos?'.http_build_query($parameters));

            if (!$response->isSuccessful()) {
                $this->registerLastError($response);

                return null;
            }

            $json   = json_decode($response->getContent(), true);
            $videos = $json['data'];

            $allVideos[] = $this->castAll($videos);
            if (null !== $callback) {
                foreach (current($allVideos) as $video) {
                    call_user_func($callback, $video);
                }
            }

            if (isset($parameters['currentPage'])) {
                break;
            }

            $pagination = $json['pagination'];
            $pagination['currentPage']++;
        } while ($pagination['pagesTotal'] > $pagination['currentPage']);
        $allVideos = call_user_func_array('array_merge', $allVideos);

        if (null === $callback) {
            return $allVideos;
        }

        return null;
    }

    /**
     * @param string $title
     * @param array $properties
     * @return Video|null
     */
    public function create($title, array $properties = array())
    {
        $response = $this->browser->post(
            '/videos',
            array(),
            json_encode(
                array_merge(
                    $properties,
                    array('title' => $title)
                )
            )
        );
        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $this->unmarshal($response);
    }

    /**
     * @param string $source Path to the file to upload
     * @param array $properties
     * @param string $videoId
     * @return Video|null
     * @throws \Buzz\Exception\RequestException
     * @throws \UnexpectedValueException
     */
    public function upload($source, array $properties = array(), $videoId = null)
    {
        if (!is_readable($source)) {
            throw new \UnexpectedValueException("'$source' must be a readable source file.");
        }

        if (null === $videoId) {
            if (!isset($properties['title'])) {
                $properties['title'] = basename($source);
            }

            $videoId = $this->create($properties['title'], $properties)->videoId;
        }

        $resource = fopen($source, 'rb');

        $stats  = fstat($resource);
        $length = $stats['size'];
        if (0 >= $length) {
            throw new \UnexpectedValueException("'$source' is empty.");
        }
        // Complete upload in a single request when file is small enough
        if ($this->chunkSize > $length) {
            $response = $this->browser->submit(
                "/videos/$videoId/source",
                array('file' => new FormUpload($source))
            );

            if (!$response->isSuccessful()) {
                $this->registerLastError($response);

                return null;
            }

            return $this->unmarshal($response);
        }

        // Split content to upload big files in multiple requests
        $copiedBytes = 0;
        stream_set_chunk_size($resource, $this->chunkSize);
        $lastResponse = null;
        do {
            $chunkPath   = tempnam(sys_get_temp_dir(), 'upload-chunk-');
            $chunk       = fopen($chunkPath, 'w+b');
            $from        = $copiedBytes;
            $copiedBytes += stream_copy_to_stream($resource, $chunk, $this->chunkSize, $copiedBytes);

            $response = $this->browser->submit(
                "/videos/$videoId/source",
                array('file' => new FormByteRangeUpload($chunkPath, $from, $copiedBytes, $length)),
                RequestInterface::METHOD_POST,
                array(
                    'Content-Range' => 'bytes '.$from.'-'.($copiedBytes - 1).'/'.$length,
                    'Expect'        => '',
                )
            );

            if ($response->getStatusCode() >= 400) {
                $this->registerLastError($response);

                return null;
            }

            $lastResponse = $this->unmarshal($response);

            fclose($chunk);
            unlink($chunkPath);

        } while ($copiedBytes < $length);

        fclose($resource);

        return $lastResponse;
    }

    /**
     * @param string $source Path to the file to upload
     * @param string $videoId
     * @return Video|null
     * @throws \Buzz\Exception\RequestException
     * @throws \UnexpectedValueException
     */
    public function uploadThumbnail($source, $videoId)
    {
        if (!is_readable($source)) {
            throw new \UnexpectedValueException("'$source' must be a readable source file.");
        }

        $resource = fopen($source, 'rb');

        $stats  = fstat($resource);
        $length = $stats['size'];
        if (0 >= $length) {
            throw new \UnexpectedValueException("'$source' is empty.");
        }

        $response = $this->browser->submit(
            "/videos/$videoId/thumbnail",
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
     * @param array $properties
     * @return Video|null
     */
    public function update($videoId, array $properties)
    {
        $response = $this->browser->patch(
            "/videos/$videoId",
            array(),
            json_encode($properties)
        );

        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $this->unmarshal($response);
    }

    /**
     * @param string $videoId
     * @param string $timecode
     * @return Video|null
     */
    public function updateThumbnailWithTimeCode($videoId, $timecode)
    {
        if (empty($timecode)) {
            throw new \UnexpectedValueException('Timecode is empty.');
        }

        $response = $this->browser->patch(
            "/videos/$videoId/thumbnail",
            array(),
            json_encode(array('timecode' => $timecode))
        );

        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $this->unmarshal($response);
    }

    /**
     * @param string $videoId
     * @return int|null
     */
    public function delete($videoId)
    {
        $response = $this->browser->delete("/videos/$videoId");

        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $response->getStatusCode();
    }

    /**
     * @param array $data
     * @return Video
     */
    protected function cast(array $data)
    {
        $video              = new Video;
        $video->videoId     = $data['videoId'];
        $video->title       = $data['title'];
        $video->description = $data['description'];
        $video->tags        = $data['tags'];
        $video->metadata    = $data['metadata'];
        $video->source      = $data['source'];
        $video->assets      = $data['assets'];
        $video->publishedAt = \DateTimeImmutable::createFromFormat(
            \DateTime::ATOM,
            $data['publishedAt']
        );

        return $video;
    }
}
