<?php


namespace ApiVideo\Client\Api;

use ApiVideo\Client\Model\Analytic\PlayerSession;
use DateTime;
use Exception;
use InvalidArgumentException;

class AnalyticsVideo extends BaseApi
{
    /**
     * @param            $videoId
     * @param null       $period
     * @param array|null $metadata
     * @param array      $parameters
     * @return PlayerSession[]|null
     */
    public function search($videoId, $period = null, array $metadata = null, array $parameters = array())
    {
        if (null !== $period) {
            $parameters['period'] = $period;
        }

        if (null !== $metadata) {
            if (!is_array($metadata)) {
                throw new InvalidArgumentException('Metadata argument must be an array');
            }

            $parameters['metadata'] = $metadata;
        }

        $parameters['currentPage'] = isset($parameters['currentPage']) ? $parameters['currentPage'] : 1;
        $parameters['pageSize'] = isset($parameters['pageSize']) ? $parameters['pageSize'] : 100;
        $items           = array();

        do {
            $response = $this->browser->get("/analytics/videos/$videoId?".http_build_query($parameters));

            if (!$response->isSuccessful()) {
                $this->registerLastError($response);

                return null;
            }

            $json = json_decode($response->getContent(), true);
            $analytics = $json['data'];
            $items[] = $this->castAll($analytics);

            if (isset($parameters['currentPage'])) {
                break;
            }

            $pagination = $json['pagination'];
            $pagination['currentPage']++;
        } while ($pagination['pagesTotal'] >= $pagination['currentPage']);

        $items = call_user_func_array('array_merge', $items);

        return $items;
    }

    /**
     * @param array $object
     * @return PlayerSession
     * @throws Exception
     */
    protected function cast(array $object)
    {
        $analyticData = new PlayerSession();

        // Build Analytic Session
        $analyticData->session->sessionId = $object['session']['sessionId'];
        $analyticData->session->loadedAt = new DateTime($object['session']['loadedAt']);
        $analyticData->session->endedAt = new DateTime($object['session']['endedAt']);
        if (isset($object['session']['metadata'])) {
            $analyticData->session->metadata = $object['session']['metadata'];
        }

        // Build Analytic Location
        $analyticData->location->country = $object['location']['country'];
        $analyticData->location->city = $object['location']['city'];

        // Build Analytic Referrer
        $analyticData->referrer->url = $object['referrer']['url'];
        $analyticData->referrer->medium = $object['referrer']['medium'];
        $analyticData->referrer->source = $object['referrer']['source'];
        $analyticData->referrer->searchTerm = $object['referrer']['searchTerm'];

        // Build Analytic Device
        $analyticData->device->type = $object['device']['type'];
        $analyticData->device->vendor = $object['device']['vendor'];
        $analyticData->device->model = $object['device']['model'];

        // Build Analytic Os
        $analyticData->os->name = $object['os']['name'];
        $analyticData->os->shortname = $object['os']['shortname'];
        $analyticData->os->version = $object['os']['version'];

        // Build Analytic Client
        $analyticData->client->type = $object['client']['type'];
        $analyticData->client->name = $object['client']['name'];
        $analyticData->client->version = $object['client']['version'];

        return $analyticData;
    }
}
