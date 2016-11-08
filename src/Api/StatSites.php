<?php

namespace SchulzeFelix\Stat\Api;

use Illuminate\Support\Collection;

class StatSites extends BaseStat
{

    public function all() : Collection
    {
        $start = 0;
        $sites = collect();

        do {
            $response = $this->performQuery('sites/all', ['start' => $start, 'results' => 5000]);
            $start += 5000;
            $sites = $sites->merge($response['Result']);

            if(!isset($response['nextpage'])) {
                break;
            }
        } while ($response['resultsreturned'] < $response['totalresults']);

        return $sites;

    }

    public function list($project_id) : Collection
    {
        $response = $this->performQuery('sites/list', ['project_id' => $project_id]);

        if ($response['resultsreturned'] == 0) {
            return collect();
        }

        return collect($response['Result']);
    }

    public function rankingDistributions($siteID, $fromDate, $toDate) : Collection
    {
        $response = $this->performQuery('sites/ranking_distributions', ['id' => $siteID, 'from_date' => $fromDate, 'to_date' => $toDate]);

        $rankDistribution = collect($response['RankDistribution']);

        if(isset($response['RankDistribution']['date'])) {
            $rankDistribution = collect([$response['RankDistribution']]);
        }

        return $rankDistribution;

    }

    public function create($projectID, $url, $dropWWWprefix = true, $dropDirectories = true)
    {
        $response = $this->performQuery('sites/create', ['project_id' => $projectID, 'url' => $url, 'drop_www_prefix' => $dropWWWprefix, 'drop_directories' => $dropDirectories]);

        return $response['Result'];
    }

    public function update($siteID, array $attributes = [])
    {
        $arguments = ['id' => $siteID] + $attributes;

        $response = $this->performQuery('sites/update', $arguments);

        return $response['Result'];

    }

    public function delete($siteID)
    {
        $response = $this->performQuery('sites/delete', ['id' => $siteID]);

        return (int) $response['Result']['Id'];
    }

}
