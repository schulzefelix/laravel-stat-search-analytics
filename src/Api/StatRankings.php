<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class StatRankings extends BaseStat
{


    public function list($keywordID, Carbon $fromDate, Carbon $toDate) : Collection
    {

        $start = 0;
        $rankings = collect();

        do {
            $response = $this->performQuery('rankings/list', ['keyword_id' => $keywordID, 'from_date' => $fromDate->toDateString(), 'to_date' => $toDate->toDateString(), 'start' => 0]);
            $start += 30;

            if($response['totalresults'] == 0) {
                break;
            }

            if($response['totalresults'] == 1) {
                $rankings->push($response['Result']);
            }

            if($response['totalresults'] > 1) {
                $rankings = $rankings->merge($response['Result']);
            }


            if (!isset($response['nextpage'])) {
                break;
            }

        } while ($response['resultsreturned'] < $response['totalresults']);


        $rankings = $rankings->transform(function ($ranking, $key) {

            return [
                'date' => Carbon::parse($ranking['date']),
                'google' => [
                    'rank' => $ranking['Google']['Rank'],
                    'url' => $ranking['Google']['Url'] ?? '',
                    'base_rank' => $ranking['Google']['BaseRank'],
                ],
                'yahoo' => [
                    'rank' => $ranking['Yahoo']['Rank'],
                    'url' => $ranking['Yahoo']['Url'] ?? '',
                    'base_rank' => $ranking['Yahoo']['BaseRank'],
                ],
                'bing' => [
                    'rank' => $ranking['Bing']['Rank'],
                    'url' => $ranking['Bing']['Url'] ?? '',
                    'base_rank' => $ranking['Bing']['BaseRank'],
                ],
            ];
        });

        return $rankings;
    }




}
