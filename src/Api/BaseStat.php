<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use SchulzeFelix\Stat\Exceptions\ApiException;
use SchulzeFelix\Stat\ExponentialBackoff;
use SchulzeFelix\Stat\Objects\StatEngineRankDistribution;
use SchulzeFelix\Stat\Objects\StatRankDistribution;
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

    /**
     * @param $method
     * @param array $parameters
     * @return mixed
     * @throws ApiException
     * @throws \Exception
     */
    public function performQuery($method, $parameters = [])
    {
        $backoff = new ExponentialBackoff(5);

        try {
            $response = $backoff->execute(function () use ($method, $parameters) {
                return $this->statClient->performQuery($method, $parameters);
            });
        } catch (ClientException $e) {
            $xml = simplexml_load_string($e->getResponse()->getBody()->getContents());
            throw ApiException::requestException($xml->__toString());
        }

        if (isset($response['Response']['responsecode']) && $response['Response']['responsecode'] == '200') {
            return $response['Response'];
        }

        throw ApiException::resultError($response['Result']);
    }

    /**
     * @param Carbon $fromDate
     * @param Carbon $toDate
     * @param int $maxDays
     * @throws ApiException
     */
    protected function checkMaximumDateRange(Carbon $fromDate, Carbon $toDate, $maxDays = 31)
    {
        if ($fromDate->diffInDays($toDate) > $maxDays) {
            throw ApiException::resultError('The maximum date range between from_date and to_date is '.$maxDays.' days.');
        }
    }

    /**
     * @param $element
     * @param string $identifier
     * @return \Illuminate\Support\Collection
     */
    protected function getCollection($element, $identifier = 'Id')
    {
        if (isset($element[$identifier])) {
            $collection = collect([$element]);
        } else {
            $collection = collect($element);
        }

        return $collection;
    }

    /**
     * @param $distribution
     * @return StatRankDistribution
     */
    protected function transformRankDistribution($distribution)
    {
        $rankDistribution = new StatRankDistribution();
        $rankDistribution->date = $distribution['date'];

        if (array_key_exists('Google', $distribution)) {
            $rankDistribution->google = new StatEngineRankDistribution([
                'one' => $distribution['Google']['One'],
                'two' => $distribution['Google']['Two'],
                'three' => $distribution['Google']['Three'],
                'four' => $distribution['Google']['Four'],
                'five' => $distribution['Google']['Five'],
                'six_to_ten' => $distribution['Google']['SixToTen'],
                'eleven_to_twenty' => $distribution['Google']['ElevenToTwenty'],
                'twenty_one_to_thirty' => $distribution['Google']['TwentyOneToThirty'],
                'thirty_one_to_forty' => $distribution['Google']['ThirtyOneToForty'],
                'forty_one_to_fifty' => $distribution['Google']['FortyOneToFifty'],
                'fifty_one_to_hundred' => $distribution['Google']['FiftyOneToHundred'],
                'non_ranking' => $distribution['Google']['NonRanking'],
            ]);
        }

        if (array_key_exists('GoogleBaseRank', $distribution)) {
            $rankDistribution->google_base_rank = new StatEngineRankDistribution([
                'one' => $distribution['GoogleBaseRank']['One'],
                'two' => $distribution['GoogleBaseRank']['Two'],
                'three' => $distribution['GoogleBaseRank']['Three'],
                'four' => $distribution['GoogleBaseRank']['Four'],
                'five' => $distribution['GoogleBaseRank']['Five'],
                'six_to_ten' => $distribution['GoogleBaseRank']['SixToTen'],
                'eleven_to_twenty' => $distribution['GoogleBaseRank']['ElevenToTwenty'],
                'twenty_one_to_thirty' => $distribution['GoogleBaseRank']['TwentyOneToThirty'],
                'thirty_one_to_forty' => $distribution['GoogleBaseRank']['ThirtyOneToForty'],
                'forty_one_to_fifty' => $distribution['GoogleBaseRank']['FortyOneToFifty'],
                'fifty_one_to_hundred' => $distribution['GoogleBaseRank']['FiftyOneToHundred'],
                'non_ranking' => $distribution['GoogleBaseRank']['NonRanking'],
            ]);
        }

        if (array_key_exists('Bing', $distribution)) {
            $rankDistribution->bing = new StatEngineRankDistribution([
                'one' => $distribution['Bing']['One'],
                'two' => $distribution['Bing']['Two'],
                'three' => $distribution['Bing']['Three'],
                'four' => $distribution['Bing']['Four'],
                'five' => $distribution['Bing']['Five'],
                'six_to_ten' => $distribution['Bing']['SixToTen'],
                'eleven_to_twenty' => $distribution['Bing']['ElevenToTwenty'],
                'twenty_one_to_thirty' => $distribution['Bing']['TwentyOneToThirty'],
                'thirty_one_to_forty' => $distribution['Bing']['ThirtyOneToForty'],
                'forty_one_to_fifty' => $distribution['Bing']['FortyOneToFifty'],
                'fifty_one_to_hundred' => $distribution['Bing']['FiftyOneToHundred'],
                'non_ranking' => $distribution['Bing']['NonRanking'],
            ]);
        }

        return $rankDistribution;
    }
}
