<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class StatTags extends BaseStat
{


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

            return $item;
        });

        return $tags;
    }

    public function rankingDistributions($tagID, $fromDate, $toDate)
    {
        $this->checkMaximumDateRange($fromDate, $toDate);

        $response = $this->performQuery('tags/ranking_distributions', ['id' => $tagID, 'from_date' => $fromDate, 'to_date' => $toDate]);

        $rankDistribution = collect($response['RankDistribution']);

        if (isset($response['RankDistribution']['date'])) {
            $rankDistribution = collect([$response['RankDistribution']]);
        }

        return $rankDistribution;
    }


}
