<?php


namespace ApiVideo\Client\Api;


use ApiVideo\Client\Model\Live;

class LiveStream extends BaseApi
{
    public function get($liveStreamId)
    {
        $response = $this->browser->get("/live-streams/$liveStreamId");
        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $this->unmarshal($response);
    }

    public function create(array $properties = array())
    {
        $response = $this->browser->post(
            '/live-streams',
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

    protected function cast(array $data)
    {
        $live                   = new Live();
        $live->liveStreamId     = $data['liveStreamId'];
        $live->name             = $data['name'];
        $live->record           = $data['record'];
        $live->streamKey        = $data['streamKey'];
        $live->assets           = $data['assets'];

        return $live;
    }

}
