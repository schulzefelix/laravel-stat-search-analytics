<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Objects\StatSite;

class StatSites extends BaseStat
{

    /**
     * @return Collection
     */
    public function all() : Collection
    {
        $start = 0;
        $sites = collect();

        do {
            $response = $this->performQuery('sites/all', ['start' => $start, 'results' => 5000]);
            $start += 5000;
            $sites = $sites->merge($response['Result']);

            if (!isset($response['nextpage'])) {
                break;
            }
        } while ($response['resultsreturned'] < $response['totalresults']);


        $sites->transform(function ($site, $key) {
            return new StatSite([
                'id' => $site['Id'],
                'project_id' => $site['ProjectId'],
                'folder_id' => $site['FolderId'],
                'folder_name' => $site['FolderName'],
                'title' => $site['Title'],
                'url' => $site['Url'],
                'synced' => $site['Synced'],
                'total_keywords' => $site['TotalKeywords'],
                'created_at' => $site['CreatedAt'],
                'updated_at' => $site['UpdatedAt'],
            ]);
        });

        return $sites;
    }

    /**
     * @param $projectID
     * @return Collection
     */
    public function list($projectID) : Collection
    {
        $response = $this->performQuery('sites/list', ['project_id' => $projectID]);

        $sites = collect();
        if ($response['resultsreturned'] == 0) {
            return $sites;
        }

        if ($response['resultsreturned'] == 1) {
            $sites->push($response['Result']);
        }

        if ($response['resultsreturned'] > 1) {
            $sites = collect($response['Result']);
        }

        $sites->transform(function ($site, $key) use ($projectID) {
            return new StatSite([
                'id' => $site['Id'],
                'project_id' => $projectID,
                'folder_id' => $site['FolderId'],
                'folder_name' => $site['FolderName'],
                'title' => $site['Title'],
                'url' => $site['Url'],
                'synced' => $site['Synced'],
                'total_keywords' => $site['TotalKeywords'],
                'created_at' => $site['CreatedAt'],
                'updated_at' => $site['UpdatedAt'],
            ]);
        });

        return $sites;
    }

    /**
     * @param $siteID
     * @param Carbon $fromDate
     * @param Carbon $toDate
     * @return Collection
     */
    public function rankingDistributions($siteID, Carbon $fromDate, Carbon $toDate) : Collection
    {
        $this->checkMaximumDateRange($fromDate, $toDate);

        $response = $this->performQuery('sites/ranking_distributions', ['id' => $siteID, 'from_date' => $fromDate->toDateString(), 'to_date' => $toDate->toDateString()]);

        $rankDistribution = collect($response['RankDistribution']);

        if (isset($response['RankDistribution']['date'])) {
            $rankDistribution = collect([$response['RankDistribution']]);
        }

        $rankDistribution->transform(function ($distribution, $key) {
            return $this->transformRankDistribution($distribution);
        });

        return $rankDistribution;
    }

    /**
     * @param $projectID
     * @param $url
     * @param bool $dropWWWprefix
     * @param bool $dropDirectories
     * @return StatSite
     */
    public function create($projectID, $url, $dropWWWprefix = true, $dropDirectories = true)
    {
        $response = $this->performQuery('sites/create', ['project_id' => $projectID, 'url' => $url, 'drop_www_prefix' => $dropWWWprefix, 'drop_directories' => $dropDirectories]);

        return new StatSite([
            'id' => $response['Result']['Id'],
            'project_id' => $response['Result']['ProjectId'],
            'title' => $response['Result']['Title'],
            'url' => $response['Result']['Url'],
            'drop_www_prefix' => $response['Result']['DropWWWPrefix'],
            'drop_directories' => $response['Result']['DropDirectories'],
            'created_at' => $response['Result']['CreatedAt'],
        ]);
    }

    /**
     * @param $siteID
     * @param array $attributes
     * @return StatSite
     */
    public function update($siteID, array $attributes = [])
    {
        $arguments = ['id' => $siteID] + $attributes;

        $response = $this->performQuery('sites/update', $arguments);

        return new StatSite([
            'id' => $response['Result']['Id'],
            'project_id' => $response['Result']['ProjectId'],
            'title' => $response['Result']['Title'],
            'url' => $response['Result']['Url'],
            'drop_www_prefix' => $response['Result']['DropWWWPrefix'],
            'drop_directories' => $response['Result']['DropDirectories'],
            'created_at' => $response['Result']['CreatedAt'],
            'updated_at' => $response['Result']['UpdatedAt'],
        ]);
    }

    /**
     * @param $siteID
     * @return int
     */
    public function delete($siteID)
    {
        $response = $this->performQuery('sites/delete', ['id' => $siteID]);

        return (int) $response['Result']['Id'];
    }
}
