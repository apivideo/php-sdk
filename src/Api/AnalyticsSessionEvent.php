<?php


namespace ApiVideo\Client\Api;


use ApiVideo\Client\Buzz\OAuthBrowser;
use ApiVideo\Client\Model\Analytic\AnalyticEvent;
use ApiVideo\Client\Model\Analytic\AnalyticSessionEvent;
use DateTime;
use Exception;

class AnalyticsSessionEvent extends BaseApi
{

    public function __construct(OAuthBrowser $browser)
    {
        parent::__construct($browser);
    }

    /**
     * @param $sessionId
     * @param array $parameters
     * @return AnalyticSessionEvent|null
     * @throws Exception
     */
    public function get($sessionId, array $parameters = array())
    {
        $params = $parameters;
        $currentPage = isset($parameters['currentPage']) ? $parameters['currentPage'] : 1;
        $params['pageSize'] = isset($parameters['pageSize']) ? $parameters['pageSize'] : 100;
        $analyticSessionEvent = null;
        $allSessionEvents = array();

        do {
            $params['currentPage'] = $currentPage;
            $response = $this->browser->get("/analytics/sessions/$sessionId/events?".http_build_query($parameters));

            if (!$response->isSuccessful()) {
                $this->registerLastError($response);

                return null;
            }

            $json = json_decode($response->getContent(), true);

            if (!$analyticSessionEvent) {
                $analyticSessionEvent = $this->cast($json);
            }

            $allSessionEvents[] = $this->buildAnalyticEventsData($json['data']);


            if (isset($parameters['currentPage'])) {
                break;
            }

            $pagination = $json['pagination'];
            $pagination['currentPage']++;
        } while ($pagination['pagesTotal'] > $pagination['currentPage']);

        $allSessionEvents = call_user_func_array('array_merge', $allSessionEvents);

        $analyticSessionEvent->events = $allSessionEvents;

        return $analyticSessionEvent;
    }

    /**
     * @param array $data
     * @return AnalyticSessionEvent
     * @throws Exception
     */
    protected function cast(array $data)
    {
        $analyticSessionEvent = new AnalyticSessionEvent();

        // Build Analytic Session
        $analyticSessionEvent->session->sessionId = $data['session']['sessionId'];
        $analyticSessionEvent->session->loadedAt = new DateTime($data['session']['loadedAt']);
        $analyticSessionEvent->session->endedAt = new DateTime($data['session']['endedAt']);

        // Build Analytic Resource
        $analyticSessionEvent->resource->type = $data['resource']['type'];
        $analyticSessionEvent->resource->id = $data['resource']['id'];

        return $analyticSessionEvent;
    }

    /**
     * @param array $events
     * @return array
     * @throws Exception
     */
    private function buildAnalyticEventsData(array $events)
    {
        $analyticEvents = array();

        foreach ($events as $event) {
            $analyticEvent = new AnalyticEvent();
            $analyticEvent->type = $event['type'];
            $analyticEvent->emittedAt = new DateTime($event['emittedAt']);
            $analyticEvent->at = isset($event['at']) ? $event['at'] : null;
            $analyticEvent->from = isset($event['from']) ? $event['from'] : null;
            $analyticEvent->to = isset($event['to']) ? $event['to'] : null;

            $analyticEvents[] = $analyticEvent;
        }

        return $analyticEvents;
    }
}