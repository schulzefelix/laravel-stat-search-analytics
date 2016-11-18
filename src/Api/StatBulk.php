<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class StatBulk extends BaseStat
{

    public function list()
    {
        $response = $this->performQuery('bulk/list');

        return collect($response['Result'])->transform(function ($job, $key) {

            return [
                'id' => $job['Id'],
                'job_type' => $job['JobType'],
                'format' => $job['Format'],
                'date' => Carbon::parse($job['Date']),
                'status' => $job['Status'],
                'url' => $job['Url'],
                'stream_url' => $job['StreamUrl'],
                'created_at' => Carbon::parse($job['CreatedAt']),
            ];

        });

    }







}
