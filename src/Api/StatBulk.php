<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Exceptions\ApiException;

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

    /**
     * @param Carbon $date
     * @param array|null $sites
     * @param string $rankType
     * @param array|null $engines
     * @param bool $currentlyTrackedOnly
     * @param bool $crawledKeywordsOnly
     * @return int
     */
    public function ranks(Carbon $date, array $sites = null, $rankType = 'highest', $engines = ['google', 'yahoo', 'bing'], bool $currentlyTrackedOnly = false , bool $crawledKeywordsOnly = false)
    {
        $this->validateBulkDate($date);

        $arguments['date'] = $date->toDateString();
        $arguments['rank_type'] = $rankType;
        $arguments['currently_tracked_only'] = $currentlyTrackedOnly;
        $arguments['crawled_keywords_only'] = $crawledKeywordsOnly;

        if( ! is_null($sites) && count($sites) > 0){
            $arguments['site_id'] = implode(',', $sites);;
        }
        if( ! is_null($engines) && count($engines) > 0){
            $arguments['engines'] = implode(',', $engines);
        }
        $response = $this->performQuery('bulk/ranks', $arguments);

        return (int) $response['Result']['Id'];
    }

    public function status($bulkJobID)
    {
        $response = $this->performQuery('bulk/status', ['id' => $bulkJobID]);

        $jobStatus = [];
        $jobStatus['id'] = $response['Result']['Id'];
        $jobStatus['job_type'] = $response['Result']['JobType'];
        $jobStatus['format'] = $response['Result']['Format'];
        $jobStatus['date'] = Carbon::parse($response['Result']['Date']);

        $jobStatus['sites'] = collect();
        if(isset($response['Result']['SiteId'])){
            $jobStatus['sites'] = collect( explode(',', $response['Result']['SiteId']));
        }
        //Current Job Status (NotStarted,InProgress,Completed,Deleted,Failed)
        $jobStatus['status'] = $response['Result']['Status'];
        $jobStatus['url'] = $response['Result']['Url'] ?? null;
        $jobStatus['stream_url'] = $response['Result']['StreamUrl'] ?? null;
        $jobStatus['created_at'] = Carbon::parse($response['Result']['CreatedAt']);

        return $jobStatus;

    }

    public function delete($bulkJobID)
    {
        $response = $this->performQuery('bulk/delete', ['id' => $bulkJobID]);

        return (int) $response['Result']['Id'];
    }

    public function siteRankingDistributions($date)
    {
        $this->validateBulkDate($date);

        $response = $this->performQuery('bulk/site_ranking_distributions', ['date' => $date->toDateString()]);
        return (int) $response['Result']['Id'];
    }

    public function tagRankingDistributions($date)
    {
        $this->validateBulkDate($date);

        $response = $this->performQuery('bulk/tag_ranking_distributions', ['date' => $date->toDateString()]);
        return (int) $response['Result']['Id'];
    }

    /**
     * @param Carbon $date
     * @throws ApiException
     */
    private function validateBulkDate(Carbon $date)
    {
        if ($date->isSameDay(Carbon::now()) or $date->isFuture()) {
            throw ApiException::resultError('You cannot create bulk jobs for the current date or dates in the future.');
        }
    }




}
