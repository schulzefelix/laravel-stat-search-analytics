<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class StatTags extends BaseStat
{

    /**
     * @param $siteID
     * @return Collection
     */
    public function list($siteID) : Collection
    {
        $response = $this->performQuery('tags/list', ['site_id' => $siteID, 'results' => 5000]);

        if ($response['resultsreturned'] == 0) {
            return collect();
        }

        $tags = collect($response['Result'])->transform(function ($item, $key) {

            if($item['Keywords'] == 'none') {
                $item['Keywords'] = collect();
            } else {
                $item['Keywords'] = collect($item['Keywords']['Id']);
            }

            return [
                'id' => (int)$item['Id'],
                'tag' => $item['Tag'],
                'type' => $item['Type'],
                'keywords' => $item['Keywords'],
            ];
        });

        return $tags;
    }

    /**
     * @param $tagID
     * @param Carbon $fromDate
     * @param Carbon $toDate
     * @return Collection
     */
    public function rankingDistributions($tagID, Carbon $fromDate, Carbon $toDate)
    {
        $this->checkMaximumDateRange($fromDate, $toDate);

        $response = $this->performQuery('tags/ranking_distributions', ['id' => $tagID, 'from_date' => $fromDate->toDateString(), 'to_date' => $toDate->toDateString()]);

        $rankDistribution = collect($response['RankDistribution']);

        if (isset($response['RankDistribution']['date'])) {
            $rankDistribution = collect([$response['RankDistribution']]);
        }

        $rankDistribution->transform(function ($distribution, $key) {
            return [
                'date' => Carbon::parse($distribution['date']),
                'google' => [
                    'one' => (int)$distribution['Google']['One'],
                    'two' => (int)$distribution['Google']['Two'],
                    'three' => (int)$distribution['Google']['Three'],
                    'four' => (int)$distribution['Google']['Four'],
                    'five' => (int)$distribution['Google']['Five'],
                    'six_to_ten' => (int)$distribution['Google']['SixToTen'],
                    'eleven_to_twenty' => (int)$distribution['Google']['ElevenToTwenty'],
                    'twenty_one_to_thirty' => (int)$distribution['Google']['TwentyOneToThirty'],
                    'thirty_one_to_forty' => (int)$distribution['Google']['ThirtyOneToForty'],
                    'forty_one_to_fifty' => (int)$distribution['Google']['FortyOneToFifty'],
                    'fifty_one_to_hundred' => (int)$distribution['Google']['FiftyOneToHundred'],
                    'non_ranking' => (int)$distribution['Google']['NonRanking'],
                ],
                'google_base_rank' => [
                    'one' => (int)$distribution['GoogleBaseRank']['One'],
                    'two' => (int)$distribution['GoogleBaseRank']['Two'],
                    'three' => (int)$distribution['GoogleBaseRank']['Three'],
                    'four' => (int)$distribution['GoogleBaseRank']['Four'],
                    'five' => (int)$distribution['GoogleBaseRank']['Five'],
                    'six_to_ten' => (int)$distribution['GoogleBaseRank']['SixToTen'],
                    'eleven_to_twenty' => (int)$distribution['GoogleBaseRank']['ElevenToTwenty'],
                    'twenty_one_to_thirty' => (int)$distribution['GoogleBaseRank']['TwentyOneToThirty'],
                    'thirty_one_to_forty' => (int)$distribution['GoogleBaseRank']['ThirtyOneToForty'],
                    'forty_one_to_fifty' => (int)$distribution['GoogleBaseRank']['FortyOneToFifty'],
                    'fifty_one_to_hundred' => (int)$distribution['GoogleBaseRank']['FiftyOneToHundred'],
                    'non_ranking' => (int)$distribution['GoogleBaseRank']['NonRanking'],
                ],
                'yahoo' => [
                    'one' => (int)$distribution['Yahoo']['One'],
                    'two' => (int)$distribution['Yahoo']['Two'],
                    'three' => (int)$distribution['Yahoo']['Three'],
                    'four' => (int)$distribution['Yahoo']['Four'],
                    'five' => (int)$distribution['Yahoo']['Five'],
                    'six_to_ten' => (int)$distribution['Yahoo']['SixToTen'],
                    'eleven_to_twenty' => (int)$distribution['Yahoo']['ElevenToTwenty'],
                    'twenty_one_to_thirty' => (int)$distribution['Yahoo']['TwentyOneToThirty'],
                    'thirty_one_to_forty' => (int)$distribution['Yahoo']['ThirtyOneToForty'],
                    'forty_one_to_fifty' => (int)$distribution['Yahoo']['FortyOneToFifty'],
                    'fifty_one_to_hundred' => (int)$distribution['Yahoo']['FiftyOneToHundred'],
                    'non_ranking' => (int)$distribution['Yahoo']['NonRanking'],
                ],
                'bing' => [
                    'one' => (int)$distribution['Bing']['One'],
                    'two' => (int)$distribution['Bing']['Two'],
                    'three' => (int)$distribution['Bing']['Three'],
                    'four' => (int)$distribution['Bing']['Four'],
                    'five' => (int)$distribution['Bing']['Five'],
                    'six_to_ten' => (int)$distribution['Bing']['SixToTen'],
                    'eleven_to_twenty' => (int)$distribution['Bing']['ElevenToTwenty'],
                    'twenty_one_to_thirty' => (int)$distribution['Bing']['TwentyOneToThirty'],
                    'thirty_one_to_forty' => (int)$distribution['Bing']['ThirtyOneToForty'],
                    'forty_one_to_fifty' => (int)$distribution['Bing']['FortyOneToFifty'],
                    'fifty_one_to_hundred' => (int)$distribution['Bing']['FiftyOneToHundred'],
                    'non_ranking' => (int)$distribution['Bing']['NonRanking'],
                ]
            ];
        });

        return $rankDistribution;
    }


}
