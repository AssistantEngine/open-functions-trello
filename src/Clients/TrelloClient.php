<?php

namespace AssistantEngine\OpenFunctions\Trello\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TrelloClient
{
    private $client;
    private $apiKey;
    private $token;
    private $baseUri = 'https://api.trello.com/1/';

    public function __construct($apiKey, $token)
    {
        $this->apiKey = $apiKey;
        $this->token = $token;
        $this->client = new Client([
            'base_uri' => $this->baseUri
        ]);
    }

    private function queryParameters(array $params = [])
    {
        $params['key'] = $this->apiKey;
        $params['token'] = $this->token;
        return http_build_query($params);
    }

    public function get($endpoint, $params = [])
    {
        try {
            $response = $this->client->get($endpoint . '?' . $this->queryParameters($params));
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function post($endpoint, $params = [])
    {
        try {
            $response = $this->client->post($endpoint, [
                'form_params' => $params + ['key' => $this->apiKey, 'token' => $this->token]
            ]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function put($endpoint, $params = [])
    {
        try {
            $response = $this->client->put($endpoint, [
                'form_params' => $params + ['key' => $this->apiKey, 'token' => $this->token]
            ]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
