<?php


namespace ApiVideo\Client\Api;


use ApiVideo\Client\Model\Player;
use Buzz\Message\Form\FormUpload;
use InvalidArgumentException;

class Players extends BaseApi
{

    /**
     * @param string $playerId
     * @return Player|null
     */
    public function get($playerId)
    {
        $response = $this->browser->get("/players/$playerId");
        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $this->unmarshal($response);
    }

    /**
     * Incrementally iterate over a collection of elements.
     * By default the elements are returned in an array, unless you pass a
     * $callback which will be called for each instance of Player.
     * Available parameters:
     *   - currentPage (int)   current pagination page
     *   - pageSize    (int)   number of elements per page
     *   - playerIds    (array) videoIds to limit the search to
     * If currentPage and pageSize are not given, the method iterates over all
     * pages of results and return an array containing all the results.
     *
     * @param array $parameters
     * @param callable $callback
     * @return Player[]|null
     */
    public function search(array $parameters = array(), $callback = null)
    {
        $params             = $parameters;
        $currentPage        = isset($parameters['currentPage']) ? $parameters['currentPage'] : 1;
        $params['pageSize'] = isset($parameters['pageSize']) ? $parameters['pageSize'] : 100;
        $allPlayers         = array();

        do {
            $params['currentPage'] = $currentPage;
            $response              = $this->browser->get('/players?'.http_build_query($parameters));
            if (!$response->isSuccessful()) {
                $this->registerLastError($response);

                return null;
            }

            $json    = json_decode($response->getContent(), true);
            $players = $json['data'];

            $allPlayers[] = $this->castAll($players);

            if (null !== $callback) {
                foreach (current($allPlayers) as $player) {
                    call_user_func($callback, $player);
                }
            }

            if (isset($parameters['currentPage'])) {
                break;
            }

            $pagination = $json['pagination'];
            $pagination['currentPage']++;
        } while ($pagination['pagesTotal'] >= $pagination['currentPage']);

        $allPlayers = call_user_func_array('array_merge', $allPlayers);

        if (null === $callback) {
            return $allPlayers;
        }

        return null;
    }

    /**
     * @param array $properties
     * @return Player
     */
    public function create(array $properties = array())
    {
        $response = $this->browser->post(
            '/players',
            array(),
            json_encode(
                $properties
            )
        );
        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $this->unmarshal($response);
    }

    /**
     * @param string $playerId
     * @param array $properties
     * @return Player
     */
    public function update($playerId, array $properties)
    {
        $response = $this->browser->patch(
            "/players/$playerId",
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
     * @param string $source Path to the file to upload
     * @param $link
     * @param $playerId
     * @return Player|null
     */
    public function uploadLogo($source, $playerId, $link = null)
    {
        if (!is_readable($source)) {
            throw new InvalidArgumentException('The source file must be readable.');
        }

        $resource = fopen($source, 'rb');

        $stats  = fstat($resource);
        $length = $stats['size'];
        if (0 >= $length) {
            throw new InvalidArgumentException("'$source' is an empty file.");
        }

        $payload = array(
            'file' => new FormUpload($source),
        );

        if (null !== $link) {
            $payload['link'] = $link;
        }

        $response = $this->browser->submit(
            "/players/$playerId/logo",
            $payload
        );

        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $this->unmarshal($response);
    }

    /**
     * @param string $playerId
     * @return int|null
     */
    public function deleteLogo($playerId)
    {
        $response = $this->browser->delete("/players/$playerId/logo");

        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $response->getStatusCode();
    }

    /**
     * @param string $playerId
     * @return int|null
     */
    public function delete($playerId)
    {
        $response = $this->browser->delete("/players/$playerId");

        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $response->getStatusCode();
    }

    /**
     * @param array $data
     * @return Player
     */
    protected function cast(array $data)
    {
        $player                        = new Player();
        $player->playerId              = $data['playerId'];
        $player->shapeMargin           = $data['shapeMargin'];
        $player->shapeRadius           = $data['shapeRadius'];
        $player->shapeAspect           = $data['shapeAspect'];
        $player->shapeBackgroundTop    = $data['shapeBackgroundTop'];
        $player->shapeBackgroundBottom = $data['shapeBackgroundBottom'];
        $player->text                  = $data['text'];
        $player->link                  = $data['link'];
        $player->linkHover             = $data['linkHover'];
        $player->linkActive            = $data['linkActive'];
        $player->trackPlayed           = $data['trackPlayed'];
        $player->trackUnplayed         = $data['trackUnplayed'];
        $player->trackBackground       = $data['trackBackground'];
        $player->backgroundTop         = $data['backgroundTop'];
        $player->backgroundBottom      = $data['backgroundBottom'];
        $player->backgroundText        = $data['backgroundText'];
        $player->enableApi             = $data['enableApi'];
        $player->enableControls        = $data['enableControls'];
        $player->forceAutoplay         = $data['forceAutoplay'];
        $player->hideTitle             = $data['hideTitle'];
        $player->forceLoop             = $data['forceLoop'];

        if (array_key_exists('assets', $data)) {
            $player->assets = $data['assets'];
        }

        return $player;
    }
}
