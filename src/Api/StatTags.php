<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Objects\StatTag;
use SchulzeFelix\Stat\Objects\StatShareOfVoice;
use SchulzeFelix\Stat\Objects\StatFrequentDomain;
use SchulzeFelix\Stat\Objects\StatShareOfVoiceSite;

class StatTags extends BaseStat
{
    /**
     * @param $siteID
     * @return Collection
     */
    public function list($siteID) : Collection
    {
        $response = $this->performQuery('tags/list', ['site_id' => $siteID, 'results' => 5000]);

        $tags = collect();

        if ($response['resultsreturned'] == 0) {
            return $tags;
        }

        if ($response['resultsreturned'] == 1) {
            $tags->push($response['Result']);
        }

        if ($response['resultsreturned'] > 1) {
            $tags = collect($response['Result']);
        }

        $tags = $tags->transform(function ($item, $key) {
            if ($item['Keywords'] == 'none') {
                $item['Keywords'] = collect();
            } else {
                $item['Keywords'] = collect($item['Keywords']['Id']);
            }

            return new StatTag([
                'id' => $item['Id'],
                'tag' => $item['Tag'],
                'type' => $item['Type'],
                'keywords' => $item['Keywords'],
            ]);
        });

        return $tags;
    }

    /**
     * @param $tagID
     * @param Carbon $fromDate
     * @param Carbon $toDate
     * @return Collection
     */
    public function rankingDistributions($tagID, Carbon $fromDate, Carbon $toDate)
    {
        $this->checkMaximumDateRange($fromDate, $toDate);

        $response = $this->performQuery('tags/ranking_distributions', ['id' => $tagID, 'from_date' => $fromDate->toDateString(), 'to_date' => $toDate->toDateString()]);

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
            $response = $this->performQuery('tags/sov', ['id' => $siteID, 'from_date' => $fromDate->toDateString(), 'to_date' => $toDate->toDateString(), 'start' => $start, 'results' => 5000]);
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
     * @param $tagID
     * @param string $engine
     * @return Collection
     */
    public function mostFrequentDomains($tagID, $engine = 'google')
    {
        $response = $this->performQuery('tags/most_frequent_domains', ['id' => $tagID, 'engine' => $engine]);

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
