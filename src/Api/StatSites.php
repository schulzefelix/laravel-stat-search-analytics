<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Objects\StatSite;
use SchulzeFelix\Stat\Objects\StatShareOfVoice;
use SchulzeFelix\Stat\Objects\StatFrequentDomain;
use SchulzeFelix\Stat\Objects\StatShareOfVoiceSite;

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

            if ($response['totalresults'] == 1) {
                $response['Result'] = [$response['Result']];
            }

            $sites = $sites->merge($response['Result']);

            if (! isset($response['nextpage'])) {
                break;
            }
        } while ($response['resultsreturned'] < $response['totalresults']);

        $sites->transform(function ($site) {
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

        $sites->transform(function ($site) use ($projectID) {
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

        $rankDistribution->transform(function ($distribution) {
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
        $response = $this->performQuery('sites/create', ['project_id' => $projectID, 'url' => rawurlencode($url), 'drop_www_prefix' => ($dropWWWprefix) ?: 0, 'drop_directories' => ($dropDirectories) ?: 0]);

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
     * @param string|null $title
     * @param string|null $url
     * @param bool|null $dropWWWprefix
     * @param bool|null $dropDirectories
     * @return StatSite
     */
    public function update($siteID, $title = null, $url = null, $dropWWWprefix = null, $dropDirectories = null)
    {
        $arguments = [];
        $arguments['id'] = $siteID;

        if (! is_null($title)) {
            $arguments['title'] = rawurlencode($title);
        }

        if (! is_null($url)) {
            $arguments['url'] = rawurlencode($url);
        }

        if (! is_null($dropWWWprefix)) {
            $arguments['drop_www_prefix'] = ($dropWWWprefix) ?: 0;
        }

        if (! is_null($dropDirectories)) {
            $arguments['drop_directories'] = ($dropDirectories) ?: 0;
        }

        $response = $this->performQuery('sites/update', $arguments);

        return new StatSite([
            'id' => $response['Result']['Id'],
            'project_id' => $response['Result']['ProjectId'],
            'title' => $response['Result']['Title'],
            'url' => $response['Result']['Url'],
            'drop_www_prefix' => $response['Result']['DropWWWPrefix'],
            'drop_directories' => $response['Result']['DropDirectories'],
            'created_at' => $response['Result']['CreatedAt'],
            'updated_at' => (isset($response['Result']['UpdatedAt'])) ? $response['Result']['UpdatedAt'] : $response['Result']['CreatedAt'],
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

    /**
     * @param $siteID
     * @param Carbon $fromDate
     * @param Carbon $toDate
     * @return Collection
     */
    public function sov($siteID, Carbon $fromDate, Carbon $toDate) : Collection
    {
        $start = 0;
        $sovSites = collect();

        do {
            $response = $this->performQuery('sites/sov', ['id' => $siteID, 'from_date' => $fromDate->toDateString(), 'to_date' => $toDate->toDateString(), 'start' => $start, 'results' => 5000]);
            $start += 5000;
            $sovSites = $sovSites->merge($response['ShareOfVoice']);

            if (! isset($response['nextpage'])) {
                break;
            }
        } while ($response['resultsreturned'] < $response['totalresults']);

        $sovSites->transform(function ($sov) {
            $shareOfVoice = new StatShareOfVoice([
                'date' => $sov['date'],
                'sites' => collect($sov['Site'])->transform(function ($site) {
                    return new StatShareOfVoiceSite([
                        'domain' => $site['Domain'],
                        'share' => (float) $site['Share'],
                        'pinned' => filter_var(Arr::get($site, 'Pinned'), FILTER_VALIDATE_BOOLEAN),
                    ]);
                }),
            ]);

            return $shareOfVoice;
        });

        return $sovSites;
    }

    /**
     * @param $siteID
     * @param string $engine
     * @return Collection
     */
    public function mostFrequentDomains($siteID, $engine = 'google')
    {
        $response = $this->performQuery('sites/most_frequent_domains', ['id' => $siteID, 'engine' => $engine]);

        $domains = collect($response['Site'])->transform(function ($site) {
            return new StatFrequentDomain([
                'domain'           => Arr::get($site, 'Domain'),
                'top_ten_results'  => Arr::get($site, 'TopTenResults'),
                'results_analyzed' => Arr::get($site, 'ResultsAnalyzed'),
                'coverage'         => Arr::get($site, 'Coverage'),
                'analyzed_on'      => Arr::get($site, 'AnalyzedOn'),
            ]);
        });

        return $domains;
    }
}
