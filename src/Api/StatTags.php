<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Objects\StatTag;

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
            if ($item['Keywords'] == 'none') {
                $item['Keywords'] = collect();
            } else {
                $item['Keywords'] = collect($item['Keywords']['Id']);
            }

            return new StatTag([
                'id' => $item['Id'],
                'tag' => $item['Tag'],
                'type' => $item['Type'],
                'keywords' => $item['Keywords'],
            ]);
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

        $rankDistribution->transform(function($distribution, $key) {
            return $this->transformRankDistribution($distribution);
        });

        return $rankDistribution;
    }
}
