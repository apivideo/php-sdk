<?php

namespace ApiVideo\Client\Api;

class Tokens extends BaseApi
{
    /**
     * Generate a token to delegate upload
     *
     * @return string
     */
    public function generate()
    {
        $response = $this->browser->post('/tokens');
        if (!$response->isSuccessful()) {
            $this->registerLastError($response);

            return null;
        }

        return $this->unmarshal($response);
    }

    protected function cast(array $data)
    {
        return $data['token'];
    }
}
