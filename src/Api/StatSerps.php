<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class StatSerps extends BaseStat
{

    public function show($keywordID, Carbon $date, $engine = 'google')
    {
        $response = $this->performQuery('serps/show', ['keyword_id' => $keywordID, 'engine' => $engine, 'date' => $date->toDateString()]);

        return collect($response['Result'])->transform(function ($ranking, $key) {

            return [
                'result_type' => $ranking['ResultType'],
                'rank' => $ranking['Rank'],
                'base_rank' => $ranking['BaseRank'],
                'url' => $ranking['Url'],
            ];

        });

    }





}
