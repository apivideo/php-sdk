<?php


namespace ApiVideo\Client\Api;


use ApiVideo\Client\Model\Analytic\PlayerSessionEvent;
use DateTime;
use Exception;

class AnalyticsSessionEvents extends BaseApi
{

    /**
     * @param       $sessionId
     * @param array $parameters
     * @return PlayerSessionEvent[]|null
     */
    public function search($sessionId, array $parameters = array())
    {
        $currentPage            = isset($parameters['currentPage']) ? $parameters['currentPage'] : 1;
        $parameters['pageSize'] = isset($parameters['pageSize']) ? $parameters['pageSize'] : 100;
        $analyticSessionEvent   = null;
        $items                  = array();

        do {
            $parameters['currentPage'] = $currentPage;
            $response                  = $this->browser->get("/analytics/sessions/$sessionId/events?".http_build_query($parameters));

            if (!$response->isSuccessful()) {
                $this->registerLastError($response);

                return null;
            }

            $json      = json_decode($response->getContent(), true);
            $analytics = $json['data'];
            $items[]   = $this->castAll($analytics);

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
     * @param array $data
     * @return PlayerSessionEvent
     * @throws Exception
     */
    protected function cast(array $data)
    {
        return new PlayerSessionEvent(
            $data['type'],
            new DateTime($data['emittedAt']),
            isset($data['at']) ? $data['at'] : null,
            isset($data['from']) ? $data['from'] : null,
            isset($data['to']) ? $data['to'] : null
        );
    }
}