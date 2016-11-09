<?php namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
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

        if (isset($response['Response']['responsecode']) && $response['Response']['responsecode'] == '200') {
            return $response['Response'];
        }

        throw ApiException::apiResultError($response['Result']);
    }

    protected function checkMaximumDateRange($fromDate, $toDate, $maxDays = 31) {
        $fromDate = Carbon::parse($fromDate);
        $toDate = Carbon::parse($toDate);

        if($fromDate->diffInDays($toDate) > $maxDays) {
            throw ApiException::apiResultError('The maximum date range between from_date and to_date is 31 days.');
        }
    }
}
