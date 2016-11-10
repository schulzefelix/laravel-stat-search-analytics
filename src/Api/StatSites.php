<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
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

            if (!isset($response['nextpage'])) {
                break;
            }
        } while ($response['resultsreturned'] < $response['totalresults']);


        $sites = $sites->transform(function ($project, $key) {
            return [
                'id' => (int)$project['Id'],
                'project_id' => (int)$project['ProjectId'],
                'folder_id' => $project['FolderId'],
                'folder_name' => $project['FolderName'],
                'title' => $project['Title'],
                'url' => $project['Url'],
                'synced' => $project['Synced'],
                'total_keywords' => (int)$project['TotalKeywords'],
                'created_at' => Carbon::parse($project['CreatedAt']),
                'updated_at' => Carbon::parse($project['UpdatedAt']),
            ];
        });

        return $sites;
    }

    public function list($project_id) : Collection
    {
        $response = $this->performQuery('sites/list', ['project_id' => $project_id]);

        if ($response['resultsreturned'] == 0) {
            return collect();
        }

        $sites = collect($response['Result'])->transform(function ($project, $key) {
            return [
                'id' => (int)$project['Id'],
                'folder_id' => $project['FolderId'],
                'folder_name' => $project['FolderName'],
                'title' => $project['Title'],
                'url' => $project['Url'],
                'synced' => $project['Synced'],
                'total_keywords' => (int)$project['TotalKeywords'],
                'created_at' => Carbon::parse($project['CreatedAt']),
                'updated_at' => Carbon::parse($project['UpdatedAt']),
            ];
        });

        return $sites;
    }

    public function rankingDistributions($siteID, $fromDate, $toDate) : Collection
    {
        $this->checkMaximumDateRange($fromDate, $toDate);

        $response = $this->performQuery('sites/ranking_distributions', ['id' => $siteID, 'from_date' => $fromDate, 'to_date' => $toDate]);

        $rankDistribution = collect($response['RankDistribution']);

        if (isset($response['RankDistribution']['date'])) {
            $rankDistribution = collect([$response['RankDistribution']]);
        }

        $rankDistribution->transform(function ($distribution, $key) {
            return [
                'date' => Carbon::parse($distribution['date']),
                'google' => [
                    'one' => (int)$distribution['Google']['One'],
                    'two' => (int)$distribution['Google']['Two'],
                    'three' => (int)$distribution['Google']['Three'],
                    'four' => (int)$distribution['Google']['Four'],
                    'five' => (int)$distribution['Google']['Five'],
                    'six_to_ten' => (int)$distribution['Google']['SixToTen'],
                    'eleven_to_twenty' => (int)$distribution['Google']['ElevenToTwenty'],
                    'twenty_one_to_thirty' => (int)$distribution['Google']['TwentyOneToThirty'],
                    'thirty_one_to_forty' => (int)$distribution['Google']['ThirtyOneToForty'],
                    'forty_one_to_fifty' => (int)$distribution['Google']['FortyOneToFifty'],
                    'fifty_one_to_hundred' => (int)$distribution['Google']['FiftyOneToHundred'],
                    'non_ranking' => (int)$distribution['Google']['NonRanking'],
                ],
                'google_base_rank' => [
                    'one' => (int)$distribution['GoogleBaseRank']['One'],
                    'two' => (int)$distribution['GoogleBaseRank']['Two'],
                    'three' => (int)$distribution['GoogleBaseRank']['Three'],
                    'four' => (int)$distribution['GoogleBaseRank']['Four'],
                    'five' => (int)$distribution['GoogleBaseRank']['Five'],
                    'six_to_ten' => (int)$distribution['GoogleBaseRank']['SixToTen'],
                    'eleven_to_twenty' => (int)$distribution['GoogleBaseRank']['ElevenToTwenty'],
                    'twenty_one_to_thirty' => (int)$distribution['GoogleBaseRank']['TwentyOneToThirty'],
                    'thirty_one_to_forty' => (int)$distribution['GoogleBaseRank']['ThirtyOneToForty'],
                    'forty_one_to_fifty' => (int)$distribution['GoogleBaseRank']['FortyOneToFifty'],
                    'fifty_one_to_hundred' => (int)$distribution['GoogleBaseRank']['FiftyOneToHundred'],
                    'non_ranking' => (int)$distribution['GoogleBaseRank']['NonRanking'],
                ],
                'yahoo' => [
                    'one' => (int)$distribution['Yahoo']['One'],
                    'two' => (int)$distribution['Yahoo']['Two'],
                    'three' => (int)$distribution['Yahoo']['Three'],
                    'four' => (int)$distribution['Yahoo']['Four'],
                    'five' => (int)$distribution['Yahoo']['Five'],
                    'six_to_ten' => (int)$distribution['Yahoo']['SixToTen'],
                    'eleven_to_twenty' => (int)$distribution['Yahoo']['ElevenToTwenty'],
                    'twenty_one_to_thirty' => (int)$distribution['Yahoo']['TwentyOneToThirty'],
                    'thirty_one_to_forty' => (int)$distribution['Yahoo']['ThirtyOneToForty'],
                    'forty_one_to_fifty' => (int)$distribution['Yahoo']['FortyOneToFifty'],
                    'fifty_one_to_hundred' => (int)$distribution['Yahoo']['FiftyOneToHundred'],
                    'non_ranking' => (int)$distribution['Yahoo']['NonRanking'],
                ],
                'bing' => [
                    'one' => (int)$distribution['Bing']['One'],
                    'two' => (int)$distribution['Bing']['Two'],
                    'three' => (int)$distribution['Bing']['Three'],
                    'four' => (int)$distribution['Bing']['Four'],
                    'five' => (int)$distribution['Bing']['Five'],
                    'six_to_ten' => (int)$distribution['Bing']['SixToTen'],
                    'eleven_to_twenty' => (int)$distribution['Bing']['ElevenToTwenty'],
                    'twenty_one_to_thirty' => (int)$distribution['Bing']['TwentyOneToThirty'],
                    'thirty_one_to_forty' => (int)$distribution['Bing']['ThirtyOneToForty'],
                    'forty_one_to_fifty' => (int)$distribution['Bing']['FortyOneToFifty'],
                    'fifty_one_to_hundred' => (int)$distribution['Bing']['FiftyOneToHundred'],
                    'non_ranking' => (int)$distribution['Bing']['NonRanking'],
                ]
            ];
        });

        return $rankDistribution;
    }

    public function create($projectID, $url, $dropWWWprefix = true, $dropDirectories = true)
    {
        $response = $this->performQuery('sites/create', ['project_id' => $projectID, 'url' => $url, 'drop_www_prefix' => $dropWWWprefix, 'drop_directories' => $dropDirectories]);

        return [
            'id' => (int)$response['Result']['Id'],
            'project_id' => (int)$response['Result']['ProjectId'],
            'title' => $response['Result']['Title'],
            'url' => $response['Result']['Url'],
            'drop_www_prefix' => (bool)$response['Result']['DropWWWPrefix'],
            'drop_directories' => (bool)$response['Result']['DropDirectories'],
            'created_at' => Carbon::parse($response['Result']['CreatedAt']),
        ];
    }

    public function update($siteID, array $attributes = [])
    {
        $arguments = ['id' => $siteID] + $attributes;

        $response = $this->performQuery('sites/update', $arguments);

        return [
            'id' => (int)$response['Result']['Id'],
            'project_id' => (int)$response['Result']['ProjectId'],
            'title' => $response['Result']['Title'],
            'url' => $response['Result']['Url'],
            'drop_www_prefix' => (bool)$response['Result']['DropWWWPrefix'],
            'drop_directories' => (bool)$response['Result']['DropDirectories'],
            'created_at' => Carbon::parse($response['Result']['CreatedAt']),
            'updated_at' => Carbon::parse($response['Result']['UpdatedAt']),
        ];
    }

    public function delete($siteID)
    {
        $response = $this->performQuery('sites/delete', ['id' => $siteID]);

        return (int) $response['Result']['Id'];
    }
}
