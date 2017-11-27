<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Objects\StatKeywordRanking;
use SchulzeFelix\Stat\Objects\StatKeywordEngineRanking;

class StatRankings extends BaseStat
{
    public function list($keywordID, Carbon $fromDate, Carbon $toDate) : Collection
    {
        $start = 0;
        $rankings = collect();

        do {
            $response = $this->performQuery('rankings/list', ['keyword_id' => $keywordID, 'from_date' => $fromDate->toDateString(), 'to_date' => $toDate->toDateString(), 'start' => 0]);
            $start += 30;

            if ($response['totalresults'] == 0) {
                break;
            }

            if ($response['totalresults'] == 1) {
                $rankings->push($response['Result']);
            }

            if ($response['totalresults'] > 1) {
                $rankings = $rankings->merge($response['Result']);
            }

            if (! isset($response['nextpage'])) {
                break;
            }
        } while ($response['resultsreturned'] < $response['totalresults']);

        $rankings = $rankings->transform(function ($ranking, $key) {
            return new StatKeywordRanking([
                'date' => $ranking['date'],
                'google' => new StatKeywordEngineRanking([
                    'rank' => $ranking['Google']['Rank'],
                    'base_rank' => $ranking['Google']['BaseRank'],
                    'url' => $ranking['Google']['Url'] ?? '',
                ]),
                'bing' => new StatKeywordEngineRanking([
                    'rank' => $ranking['Bing']['Rank'],
                    'url' => $ranking['Bing']['Url'] ?? '',
                    'base_rank' => $ranking['Bing']['BaseRank'],
                ]),
            ]);
        });

        return $rankings;
    }
}
