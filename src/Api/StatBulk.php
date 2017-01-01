<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use SchulzeFelix\Stat\Exceptions\ApiException;
use SchulzeFelix\Stat\Objects\StatBulkJob;
use SchulzeFelix\Stat\Objects\StatKeyword;
use SchulzeFelix\Stat\Objects\StatKeywordEngineRanking;
use SchulzeFelix\Stat\Objects\StatKeywordRanking;
use SchulzeFelix\Stat\Objects\StatKeywordStats;
use SchulzeFelix\Stat\Objects\StatLocalSearchTrend;
use SchulzeFelix\Stat\Objects\StatProject;
use SchulzeFelix\Stat\Objects\StatSite;
use SchulzeFelix\Stat\Objects\StatTag;

class StatBulk extends BaseStat
{
    public function list()
    {
        $response = $this->performQuery('bulk/list');

        $bulkJobs = collect();

        if ($response['resultsreturned'] == 0) {
            return $bulkJobs;
        }

        if ($response['resultsreturned'] == 1) {
            $bulkJobs->push($response['Result']);
        }

        if ($response['resultsreturned'] > 1) {
            $bulkJobs = collect($response['Result']);
        }

        return $bulkJobs->transform(function ($job, $key) {
            return $this->transformBulkJobStatus($job);
        });
    }

    /**
     * Schedule the creation of a new bulk export job for ranks.
     *
     * @param Carbon $date Note that you cannot create bulk jobs for the current date or dates in the future.
     * @param array|null $sites If no site IDs are passed in, ranks are reported for all sites in the system on the given date.
     * @param string $rankType This parameter changes the call between getting the highest ranks for the keywords for the date with the value 'highest', or getting all the ranks for each engine for a keyword for a date with the value 'all'.
     * @param array|null $engines This parameter lets you choose which search engines to include in the export, defaulting to Google, Yahoo, and Bing. Engines can be passed in a array to get multiple. ['google', 'yahoo', 'bing']
     * @param bool $currentlyTrackedOnly This parameter will cause the API to output only keywords which currently have tracking on at the time the API request is generated.
     * @param bool $crawledKeywordsOnly This parameter causes the API to only include output for keywords that were crawled on the date parameter provided.
     * @return int
     */
    public function ranks(Carbon $date, array $sites = null, $rankType = 'highest', $engines = null, bool $currentlyTrackedOnly = false, bool $crawledKeywordsOnly = false)
    {
        $this->validateBulkDate($date);

        $arguments['date'] = $date->toDateString();
        $arguments['rank_type'] = $rankType;
        $arguments['currently_tracked_only'] = $currentlyTrackedOnly;
        $arguments['crawled_keywords_only'] = $crawledKeywordsOnly;

        if (! is_null($sites) && count($sites) > 0) {
            $arguments['site_id'] = implode(',', $sites);
            ;
        }
        if (! is_null($engines) && count($engines) > 0) {
            $arguments['engines'] = implode(',', $engines);
        }
        $response = $this->performQuery('bulk/ranks', $arguments);

        return (int) $response['Result']['Id'];
    }

    public function status($bulkJobID)
    {
        $response = $this->performQuery('bulk/status', ['id' => $bulkJobID]);

        return $this->transformBulkJobStatus($response['Result']);
    }

    public function delete($bulkJobID)
    {
        $response = $this->performQuery('bulk/delete', ['id' => $bulkJobID]);

        return (int)$response['Result']['Id'];
    }

    public function siteRankingDistributions($date)
    {
        $this->validateBulkDate($date);

        $response = $this->performQuery('bulk/site_ranking_distributions', ['date' => $date->toDateString()]);
        return (int)$response['Result']['Id'];
    }

    public function tagRankingDistributions($date)
    {
        $this->validateBulkDate($date);

        $response = $this->performQuery('bulk/tag_ranking_distributions', ['date' => $date->toDateString()]);
        return (int) $response['Result']['Id'];
    }

    public function get($bulkJobID)
    {
        $bulkStatus = $this->status($bulkJobID);

        if ($bulkStatus['status'] != 'Completed') {
            throw ApiException::resultError('Bulk Job is not completed. Current status: ' . $bulkJobID['status'] . '.');
        }

        $bulkStream = $this->statClient->downloadBulkJobStream($bulkStatus['stream_url']);

        return $this->parseBulkJob($bulkStream['Response']);
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


    private function parseBulkJob($bulkStream)
    {
        $projects = $this->getCollection($bulkStream['Project']);

        $projects->transform(function ($project, $key) {
            return $this->transformProject($project);
        });

        return $projects;
    }

    private function transformProject($project)
    {
        $transformedProject = new StatProject();
        $transformedProject->id = $project['Id'];
        $transformedProject->name = $project['Name'];
        $transformedProject->total_sites = $project['TotalSites'];
        $transformedProject->created_at = $project['CreatedAt'];

        if ($project['TotalSites'] == 0) {
            $transformedProject->sites = collect();
        }
        if ($project['TotalSites'] == 1) {
            $transformedProject->sites = collect([$project['Site']]);
        }
        if ($project['TotalSites'] > 1) {
            $transformedProject->sites = collect($project['Site']);
        }
        $transformedProject->sites->transform(function ($site, $key) {
            return $this->transformSite($site);
        });

        return $transformedProject;
    }


    private function transformSite($site)
    {
        $transformedSite = new StatSite();
        $transformedSite->id = $site['Id'];
        $transformedSite->url = $site['Url'];
        $transformedSite->total_keywords = $site['TotalKeywords'];
        $transformedSite->created_at = $site['CreatedAt'];

        if (array_key_exists('Keyword', $site)) {
            $transformedSite->keywords = collect($site['Keyword'])->transform(function ($keyword, $key) {
                return $this->transformKeyword($keyword);
            });
        }

        if (array_key_exists('RankDistribution', $site)) {
            $transformedSite->rank_distribution = $this->transformRankDistribution($site['RankDistribution']);
        }

        if (array_key_exists('Tag', $site)) {
            $transformedSite->tags = $this->getCollection($site['Tag'])->transform(function ($tag, $key) {
                return $this->transformTag($tag);
            });
        }


        return $transformedSite;
    }

    private function transformKeyword($keyword)
    {
        $modifiedKeyword = new StatKeyword();
        $modifiedKeyword->id = $keyword['Id'];
        $modifiedKeyword->keyword = $keyword['Keyword'];
        $modifiedKeyword->keyword_market = $keyword['KeywordMarket'];
        $modifiedKeyword->keyword_location = $keyword['KeywordLocation'];
        $modifiedKeyword->keyword_device = $keyword['KeywordDevice'];
        $modifiedKeyword->keyword_categories = $keyword['KeywordCategories'];

        if (is_null($keyword['KeywordTags'])) {
            $modifiedKeyword->keyword_tags = collect();
        } else {
            $modifiedKeyword->keyword_tags = collect(explode(',', $keyword['KeywordTags']));
        }

        if (is_null($keyword['KeywordStats'])) {
            $modifiedKeyword->keyword_stats = null;
        } else {
            $localTrends = collect($keyword['KeywordStats']['LocalSearchTrendsByMonth'])->map(function ($searchVolume, $month) {
                return new StatLocalSearchTrend([
                    'month' => strtolower($month),
                    'search_volume' => ($searchVolume == '-') ? null : $searchVolume,
                ]);
            });

            $modifiedKeyword->keyword_stats = new StatKeywordStats([
                'advertiser_competition' => $keyword['KeywordStats']['AdvertiserCompetition'],
                'global_search_volume' => $keyword['KeywordStats']['GlobalSearchVolume'],
                'targeted_search_volume' => $keyword['KeywordStats']['TargetedSearchVolume'],
                'cpc' => $keyword['KeywordStats']['CPC'],
                'local_search_trends_by_month' => $localTrends->values(),
            ]);
        }

        $modifiedKeyword->created_at = $keyword['CreatedAt'];

        $modifiedKeyword->ranking = new StatKeywordRanking([
            'date' => $keyword['Ranking']['date'],
            'type' => $keyword['Ranking']['type']
        ]);

        if (array_key_exists('Google', $keyword['Ranking'])) {
            $modifiedKeyword->ranking->google = $this->analyzeRanking($keyword['Ranking']['Google'], $keyword['Ranking']['type']);
        }

        if (array_key_exists('Yahoo', $keyword['Ranking'])) {
            $modifiedKeyword->ranking->yahoo = $this->analyzeRanking($keyword['Ranking']['Yahoo'], $keyword['Ranking']['type']);
        }

        if (array_key_exists('Bing', $keyword['Ranking'])) {
            $modifiedKeyword->ranking->bing = $this->analyzeRanking($keyword['Ranking']['Bing'], $keyword['Ranking']['type']);
        }

        return $modifiedKeyword;
    }

    private function analyzeRanking($rankingForEngine, $rankingType)
    {
        if ($rankingType == 'highest') {
            return $this->transformRanking($rankingForEngine);
        }

        if (is_null($rankingForEngine['Result'])) {
            return null;
        }

        $rankings = $this->getCollection($rankingForEngine['Result'], 'Rank');

        $rankings->transform(function ($ranking, $key) {
            return $this->transformRanking($ranking);
        });

        return $rankings;
    }

    private function transformRanking($ranking)
    {
        $transformedRanking = new StatKeywordEngineRanking();
        $transformedRanking->rank = $ranking['Rank'];
        if (array_key_exists('BaseRank', $ranking)) {
            $transformedRanking->base_rank = $ranking['BaseRank'];
        }
        $transformedRanking->url = $ranking['Url'];

        return $transformedRanking;
    }

    private function transformTag($tag)
    {
        $modifiedTag = new StatTag();
        $modifiedTag->id = $tag['Id'];
        $modifiedTag->tag = $tag['Tag'];

        if (isset($tag['RankDistribution'])) {
            $modifiedTag->rank_distribution = $this->transformRankDistribution($tag['RankDistribution']);
        } else {
            $modifiedTag->rank_distribution = null;
        }

        return $modifiedTag;
    }

    private function transformBulkJobStatus($job)
    {
        $bulkJob = new StatBulkJob();
        $bulkJob->id = $job['Id'];
        $bulkJob->job_type = $job['JobType'];
        $bulkJob->format = $job['Format'];

        if (array_has($job, ['Project', 'Folder', 'SiteTitle', 'SiteUrl'])) {
            $bulkJob->project = $job['Project'];
            $bulkJob->folder = $job['Folder'];
            $bulkJob->site_title = $job['SiteTitle'];
            $bulkJob->site_url = $job['SiteUrl'];
        }

        $bulkJob->date = $job['Date'];

        $bulkJob->sites = collect();
        if (array_has($job, 'SiteId')) {
            $bulkJob->sites = collect(explode(',', $job['SiteId']))
                ->transform(function ($site, $key) {
                    return (int)$site;
                });
        }
        $bulkJob->status = $job['Status'];
        $bulkJob->url = array_get($job, 'Url', null);
        $bulkJob->stream_url = array_get($job, 'StreamUrl', null);
        $bulkJob->created_at = $job['CreatedAt'];

        return $bulkJob;
    }
}
