<?php namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use SchulzeFelix\Stat\Exceptions\ApiException;
use SchulzeFelix\Stat\Exceptions\RequestException;
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

    protected function checkMaximumDateRange(Carbon $fromDate, Carbon $toDate, $maxDays = 31)
    {

        if($fromDate->diffInDays($toDate) > $maxDays) {
            throw ApiException::resultError('The maximum date range between from_date and to_date is '.$maxDays.' days.');
        }
    }

    protected function transformRankDistribution($distribution)
    {
        return new StatRankDistribution([
            'date' => $distribution['date'],
            'google' => new StatEngineRankDistribution([
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
            ]),
            'google_base_rank' => new StatEngineRankDistribution([
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
            ]),
            'yahoo' => new StatEngineRankDistribution([
                'one' => $distribution['Yahoo']['One'],
                'two' => $distribution['Yahoo']['Two'],
                'three' => $distribution['Yahoo']['Three'],
                'four' => $distribution['Yahoo']['Four'],
                'five' => $distribution['Yahoo']['Five'],
                'six_to_ten' => $distribution['Yahoo']['SixToTen'],
                'eleven_to_twenty' => $distribution['Yahoo']['ElevenToTwenty'],
                'twenty_one_to_thirty' => $distribution['Yahoo']['TwentyOneToThirty'],
                'thirty_one_to_forty' => $distribution['Yahoo']['ThirtyOneToForty'],
                'forty_one_to_fifty' => $distribution['Yahoo']['FortyOneToFifty'],
                'fifty_one_to_hundred' => $distribution['Yahoo']['FiftyOneToHundred'],
                'non_ranking' => $distribution['Yahoo']['NonRanking'],
            ]),
            'bing' => new StatEngineRankDistribution([
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
            ]),
        ]);
    }
}
