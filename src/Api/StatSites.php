<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Objects\StatEngineRankDistribution;
use SchulzeFelix\Stat\Objects\StatRankDistribution;
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

        if ($response['resultsreturned'] == 0) {
            return collect();
        }

        $sites = collect($response['Result'])->transform(function ($site, $key) use($projectID) {
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
            return new StatRankDistribution([
                'date' => $distribution['date'],
                'google' => new StatEngineRankDistribution([
                    'one' => $distribution['Google']['One'],
                    'two' => $distribution['Google']['Two'],
                    'three' => $distribution['Google']['Three'],
                    'four' => $distribution['Google']['Four'],
                    'five' => $distribution['Google']['Five'],
                    'six_to_ten' => $distribution['Google']['SixToTen'],
                    'eleven_to_twenty' => $distribution['Google']['ElevenToTwenty'],
                    'twenty_one_to_thirty' => $distribution['Google']['TwentyOneToThirty'],
                    'thirty_one_to_forty' => $distribution['Google']['ThirtyOneToForty'],
                    'forty_one_to_fifty' => $distribution['Google']['FortyOneToFifty'],
                    'fifty_one_to_hundred' => $distribution['Google']['FiftyOneToHundred'],
                    'non_ranking' => $distribution['Google']['NonRanking'],
                ]),
                'google_base_rank' => new StatEngineRankDistribution([
                    'one' => $distribution['GoogleBaseRank']['One'],
                    'two' => $distribution['GoogleBaseRank']['Two'],
                    'three' => $distribution['GoogleBaseRank']['Three'],
                    'four' => $distribution['GoogleBaseRank']['Four'],
                    'five' => $distribution['GoogleBaseRank']['Five'],
                    'six_to_ten' => $distribution['GoogleBaseRank']['SixToTen'],
                    'eleven_to_twenty' => $distribution['GoogleBaseRank']['ElevenToTwenty'],
                    'twenty_one_to_thirty' => $distribution['GoogleBaseRank']['TwentyOneToThirty'],
                    'thirty_one_to_forty' => $distribution['GoogleBaseRank']['ThirtyOneToForty'],
                    'forty_one_to_fifty' => $distribution['GoogleBaseRank']['FortyOneToFifty'],
                    'fifty_one_to_hundred' => $distribution['GoogleBaseRank']['FiftyOneToHundred'],
                    'non_ranking' => $distribution['GoogleBaseRank']['NonRanking'],
                ]),
                'yahoo' => new StatEngineRankDistribution([
                    'one' => $distribution['Yahoo']['One'],
                    'two' => $distribution['Yahoo']['Two'],
                    'three' => $distribution['Yahoo']['Three'],
                    'four' => $distribution['Yahoo']['Four'],
                    'five' => $distribution['Yahoo']['Five'],
                    'six_to_ten' => $distribution['Yahoo']['SixToTen'],
                    'eleven_to_twenty' => $distribution['Yahoo']['ElevenToTwenty'],
                    'twenty_one_to_thirty' => $distribution['Yahoo']['TwentyOneToThirty'],
                    'thirty_one_to_forty' => $distribution['Yahoo']['ThirtyOneToForty'],
                    'forty_one_to_fifty' => $distribution['Yahoo']['FortyOneToFifty'],
                    'fifty_one_to_hundred' => $distribution['Yahoo']['FiftyOneToHundred'],
                    'non_ranking' => $distribution['Yahoo']['NonRanking'],
                ]),
                'bing' => new StatEngineRankDistribution([
                    'one' => $distribution['Bing']['One'],
                    'two' => $distribution['Bing']['Two'],
                    'three' => $distribution['Bing']['Three'],
                    'four' => $distribution['Bing']['Four'],
                    'five' => $distribution['Bing']['Five'],
                    'six_to_ten' => $distribution['Bing']['SixToTen'],
                    'eleven_to_twenty' => $distribution['Bing']['ElevenToTwenty'],
                    'twenty_one_to_thirty' => $distribution['Bing']['TwentyOneToThirty'],
                    'thirty_one_to_forty' => $distribution['Bing']['ThirtyOneToForty'],
                    'forty_one_to_fifty' => $distribution['Bing']['FortyOneToFifty'],
                    'fifty_one_to_hundred' => $distribution['Bing']['FiftyOneToHundred'],
                    'non_ranking' => $distribution['Bing']['NonRanking'],
                ]),
            ]);
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
