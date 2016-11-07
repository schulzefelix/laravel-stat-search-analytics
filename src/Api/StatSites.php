<?php

namespace SchulzeFelix\Stat\Api;

use Illuminate\Support\Collection;

class StatSites extends BaseStat
{

    public function list($project_id) : Collection
    {
        $response = $this->performQuery('sites/list', ['project_id' => $project_id]);

        if ($response['resultsreturned'] == 0) {
            return collect();
        }

        return collect($response['Result']);
    }
}
