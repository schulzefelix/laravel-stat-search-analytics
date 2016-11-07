<?php namespace SchulzeFelix\Stat\Api;

use SchulzeFelix\Stat\Exceptions\ApiException;
use SchulzeFelix\Stat\StatClient;

class BaseStat
{
    /**
     * @var StatClient
     */
    private $statClient;

    /**
     * BaseStat constructor.
     * @param StatClient $statClient
     */
    public function __construct(StatClient $statClient)
    {
        $this->statClient = $statClient;
    }


    public function performQuery($method, $parameters = [])
    {
        $response = $this->statClient->performQuery(
            $method,
            $parameters
        );

        if(isset($response['Response']['responsecode']) && $response['Response']['responsecode'] == '200')
        {
            return $response['Response'];
        }

        throw ApiException::apiResultError($response['Result']);

    }
}