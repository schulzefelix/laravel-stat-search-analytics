<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Objects\StatSerpItem;

class StatSerps extends BaseStat
{

    /**
     * @param $keywordID
     * @param Carbon $date
     * @param string $engine
     * @return Collection
     */
    public function show($keywordID, Carbon $date, $engine = 'google') : Collection
    {
        $response = $this->performQuery('serps/show', ['keyword_id' => $keywordID, 'engine' => $engine, 'date' => $date->toDateString()]);

        return collect($response['Result'])->transform(function ($ranking, $key) {

            return new StatSerpItem([
                'result_type' => $ranking['ResultType'],
                'rank' => $ranking['Rank'],
                'base_rank' => $ranking['BaseRank'],
                'url' => $ranking['Url'],
            ]);

        });

    }





}
