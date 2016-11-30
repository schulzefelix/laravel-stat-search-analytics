<?php

namespace SchulzeFelix\Stat\Api;

use Illuminate\Support\Collection;
use SchulzeFelix\Stat\Objects\StatKeyword;
use SchulzeFelix\Stat\Objects\StatKeywordEngineRanking;
use SchulzeFelix\Stat\Objects\StatKeywordRanking;
use SchulzeFelix\Stat\Objects\StatKeywordStats;
use SchulzeFelix\Stat\Objects\StatLocalSearchTrend;

class StatKeywords extends BaseStat
{
    /**
     * @param $siteID
     * @return Collection
     */
    public function list($siteID) : Collection
    {
        $start = 0;
        $keywords = collect();

        do {
            $response = $this->performQuery('keywords/list', ['site_id' => $siteID, 'start' => $start, 'results' => 5000 ]);
            $start += 5000;

            if ($response['totalresults'] == 0) {
                break;
            }

            if (isset($response['Result']['Id'])) {
                $keywords->push($response['Result']);
            }

            $keywords = $keywords->merge($response['Result']);

            if (!isset($response['nextpage'])) {
                break;
            }
        } while ($response['resultsreturned'] < $response['totalresults']);


        $keywords = $keywords->transform(function ($keyword) {
            return $this->transformListedKeyword($keyword);
        });

        return $keywords;
    }

    /**
     * @param $siteID
     * @param $market
     * @param array $keywords
     * @param array|null $tags
     * @param null $location
     * @param string $device
     * @return Collection
     */
    public function create($siteID, $market, array $keywords, array $tags = null, $location = null, $device = 'Desktop') : Collection
    {
        $arguments['site_id'] = $siteID;
        $arguments['market'] = $market;
        $arguments['device'] = $device;
        $arguments['type'] = 'regular';

        $keywords = array_map(function ($keyword) {
            $keyword = str_replace(',', '\,', $keyword);
            $keyword = rawurlencode($keyword);
            return $keyword;
        }, $keywords);
        $arguments['keyword'] = implode(',', $keywords);

        if (! is_null($tags) && count($tags) > 0) {
            $tags = array_map(function ($tag) {
                $tag = str_replace(',', '-', $tag);
                $tag = rawurlencode($tag);
                return $tag;
            }, $tags);

            $arguments['tag'] = implode(',', $tags);
        }

        if (! is_null($location) && $location != '') {
            $arguments['location'] = $location;
        }

        $response = $this->performQuery('keywords/create', $arguments);

        $keywords = collect();

        if ($response['resultsreturned'] == 0) {
            return $keywords;
        }
        if ($response['resultsreturned'] == 1) {
            $keywords->push($response['Result']);
        } else {
            $keywords = collect($response['Result']);
        }

        return $keywords->transform(function ($keyword) {
            return $this->transformCreatedKeyword($keyword);
        });
    }

    /**
     * @param int|array $id
     * @return Collection
     */
    public function delete($id)
    {
        if (!is_array($id)) {
            $id = [$id];
        }

        $ids = implode(',', $id);

        $response = $this->performQuery('keywords/delete', ['id' => $ids]);

        if (isset($response['Result']['Id'])) {
            return collect($response['Result']['Id']);
        }


        return collect($response['Result'])->transform(function ($keywordID) {
            return $keywordID['Id'];
        });
    }


    /**
     * @param $keyword
     * @return StatKeyword
     */
    protected function transformCreatedKeyword($keyword)
    {
        return new StatKeyword([
            'id' => $keyword['Id'],
            'keyword' => $keyword['Keyword'],
            'keyword_market' => $keyword['KeywordMarket'],
            'keyword_location' => $keyword['KeywordLocation'],
            'keyword_device' => $keyword['KeywordDevice'],
            'created_at' => $keyword['CreatedAt'],
        ]);
    }


    /**
     * @param $keyword
     * @return StatKeyword
     */
    protected function transformListedKeyword($keyword)
    {
        $modifiedKeyword = new StatKeyword();
        $modifiedKeyword->id = $keyword['Id'];
        $modifiedKeyword->keyword = $keyword['Keyword'];
        $modifiedKeyword->keyword_market = $keyword['KeywordMarket'];
        $modifiedKeyword->keyword_location = $keyword['KeywordLocation'];
        $modifiedKeyword->keyword_device = $keyword['KeywordDevice'];


        if ($keyword['KeywordTags'] == 'none') {
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
                'regional_search_volume' => $keyword['KeywordStats']['RegionalSearchVolume'],
                'cpc' => $keyword['KeywordStats']['CPC'],
                'local_search_trends_by_month' => $localTrends->values(),
            ]);
        }

        if (is_null($keyword['KeywordRanking'])) {
            $modifiedKeyword->keyword_ranking = null;
        } else {
            $modifiedKeyword->keyword_ranking = new StatKeywordRanking([
                'date' => $keyword['KeywordRanking']['date'],
                'google' => new StatKeywordEngineRanking([
                    'rank' => $keyword['KeywordRanking']['Google']['Rank'],
                    'base_rank' => $keyword['KeywordRanking']['Google']['BaseRank'],
                    'url' => $keyword['KeywordRanking']['Google']['Url'],
                ]),
                'yahoo' => new StatKeywordEngineRanking([
                    'rank' => $keyword['KeywordRanking']['Yahoo']['Rank'],
                    'url' => $keyword['KeywordRanking']['Yahoo']['Url'],
                ]),
                'bing' => new StatKeywordEngineRanking([
                    'rank' => $keyword['KeywordRanking']['Bing']['Rank'],
                    'url' => $keyword['KeywordRanking']['Bing']['Url'],
                ]),
            ]);
        }

        $modifiedKeyword->created_at = $keyword['CreatedAt'];

        return $modifiedKeyword;
    }
}
