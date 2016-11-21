<?php namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use SchulzeFelix\Stat\Exceptions\ApiException;
use SchulzeFelix\Stat\Exceptions\RequestException;
use SchulzeFelix\Stat\StatClient;

class BaseStat
{
    /**
     * @var StatClient
     */
    protected $statClient;

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
        try
        {
            $response = $this->statClient->performQuery($method, $parameters);
        }
        catch(ClientException $e)
        {
            $xml = simplexml_load_string($e->getResponse()->getBody()->getContents());
            throw ApiException::requestException($xml->__toString());
        }

        if (isset($response['Response']['responsecode']) && $response['Response']['responsecode'] == '200') {
            return $response['Response'];
        }

        throw ApiException::resultError($response['Result']);
    }

    protected function checkMaximumDateRange(Carbon $fromDate, Carbon $toDate, $maxDays = 31) {

        if($fromDate->diffInDays($toDate) > $maxDays) {
            throw ApiException::resultError('The maximum date range between from_date and to_date is '.$maxDays.' days.');
        }
    }
}
