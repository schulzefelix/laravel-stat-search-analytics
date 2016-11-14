<?php

namespace SchulzeFelix\Stat;

use GuzzleHttp\Client;

class StatClient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Sistrix constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function performQuery(string $method, array $parameters)
    {
        $request = $this->buildRequest($method, $parameters);
        dd($request);
        $response = $this->client->get($request);
        return json_decode($response->getBody()->getContents(), true);
    }

    protected function buildRequest($method, $parameters = [])
    {
        $parameterString = '?format=json';

        foreach ($parameters as $parameter => $value) {
            $parameterString .= '&' . $parameter . '=' . $value;
        }

        $request = $method . $parameterString;

        return $request;
    }
}
