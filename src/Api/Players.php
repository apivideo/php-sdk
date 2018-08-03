<?php


namespace ApiVideo\Client\Api;


use ApiVideo\Client\Model\Player;

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
        } while ($pagination['pagesTotal'] > $pagination['currentPage']);

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
        $player                           = new Player();
        $player->playerId                 = $data['playerId'];
        $player->enableApi                = $data['enableApi'];
        $player->hideTitle                = $data['hideTitle'];
        $player->controlLogo              = $data['controlLogo'];
        $player->buttonRadius             = $data['buttonRadius'];
        $player->controlMargin            = $data['controlMargin'];
        $player->forceAutoplay            = $data['forceAutoplay'];
        $player->controlLogoUrl           = $data['controlLogoUrl'];
        $player->enableControls           = $data['enableControls'];
        $player->panelTextHover           = $data['panelTextHover'];
        $player->scrollbarThumb           = $data['scrollbarThumb'];
        $player->scrollbarTrack           = $data['scrollbarTrack'];
        $player->buttonTextHover          = $data['buttonTextHover'];
        $player->enableInfoPanel          = $data['enableInfoPanel'];
        $player->panelTextActive          = $data['panelTextActive'];
        $player->buttonTextActive         = $data['buttonTextActive'];
        $player->enableSharePanel         = $data['enableSharePanel'];
        $player->buttonLightEffect        = $data['buttonLightEffect'];
        $player->panelTextInactive        = $data['panelTextInactive'];
        $player->trackbarPlayedTop        = $data['trackbarPlayedTop'];
        $player->trackbarTextColor        = $data['trackbarTextColor'];
        $player->buttonTextInactive       = $data['buttonTextInactive'];
        $player->panelBackgroundTop       = $data['panelBackgroundTop'];
        $player->buttonBackgroundTop      = $data['buttonBackgroundTop'];
        $player->enableDownloadPanel      = $data['enableDownloadPanel'];
        $player->enableSettingsPanel      = $data['enableSettingsPanel'];
        $player->trackbarPlayedBottom     = $data['trackbarPlayedBottom'];
        $player->panelBackgroundBottom    = $data['panelBackgroundBottom'];
        $player->trackbarBackgroundTop    = $data['trackbarBackgroundTop'];
        $player->buttonBackgroundBottom   = $data['buttonBackgroundBottom'];
        $player->trackbarBackgroundBottom = $data['trackbarBackgroundBottom'];

        return $player;
    }
}
