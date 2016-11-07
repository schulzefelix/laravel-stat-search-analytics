<?php

namespace SchulzeFelix\Stat;

use GuzzleHttp\Client;

class StatClientFactory
{
    public static function createForConfig(array $statConfig): StatClient
    {
        $guzzleClient = new Client(
            [
                'headers' => [
                    'User-Agent' => 'Laravel Stat Search Analytics Client',
                    'Accept-Encoding' => 'gzip, deflate, sdch'
                ],
                'base_uri' => self::buildBaseUri($statConfig),
                'timeout'  => 60.0,
            ]
        );

        $client = new StatClient($guzzleClient);
        return $client;
    }


    protected static function buildBaseUri(array $statConfig)
    {
        $baseUri = sprintf('https://%s/api/v2/%s/', $statConfig['subdomain'], $statConfig['key']);
        return $baseUri;
    }
}
