<?php


namespace Libcast\Client\Api;


use ApiVideo\Client\Buzz\OAuthBrowser;
use Buzz\Message\MessageInterface;
use Libcast\Client\Model\Player;

class Players
{
    /** @var OAuthBrowser */
    private $browser;

    /**
     * @param OAuthBrowser $browser
     */
    public function __construct(OAuthBrowser $browser)
    {
        $this->browser = $browser;
    }

    /**
     * @param string $playerId
     * @return Player
     */
    public function get($playerId)
    {
        return $this->unmarshal($this->browser->get("/players/$playerId"));
    }

    /**
     * Incrementally iterate over a collection of elements.
     * By default the elements are returned in an array, unless you pass a
     * $callback which will be called for each instance of Video.
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
    public function getList(array $parameters = array(), $callback = null)
    {
        $params             = $parameters;
        $currentPage        = isset($parameters['currentPage']) ? $parameters['currentPage'] : 1;
        $params['pageSize'] = isset($parameters['pageSize']) ? $parameters['pageSize'] : 100;
        $allPlayers          = array();

        do {
            $params['currentPage'] = $currentPage;
            $response              = $this->browser->get('/players?'.http_build_query($parameters));
            $json                  = json_decode($response->getContent(), true);
            $players                = $json['data'];
            if (null === $callback) {
                $allPlayers[] = $this->castAll($players);
            } else {
                foreach ($players as $player) {
                    $callback($this->unmarshal($player));
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
        return $this->unmarshal(
            $this->browser->post(
                '/players',
                array(),
                json_encode(
                    $properties
                )
            )
        );
    }

    /**
     * @param string $playerId
     * @param array $properties
     * @return Player
     */
    public function update($playerId, array $properties)
    {
        return $this->unmarshal(
            $this->browser->patch(
                "/players/$playerId",
                array(),
                json_encode($properties)
            )
        );
    }

    /**
     * @param \Buzz\Message\MessageInterface $message
     * @return Player
     */
    private function unmarshal(MessageInterface $message)
    {
        return $this->cast(json_decode($message->getContent(), true));
    }

    /**
     * @param array $videos
     * @return Players[]
     */
    private function castAll(array $videos)
    {
        return array_map(array($this, 'cast'), $videos);
    }

    /**
     * @param array $data
     * @return Player
     */
    private function cast(array $data)
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
