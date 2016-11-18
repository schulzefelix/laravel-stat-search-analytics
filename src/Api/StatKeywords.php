<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;

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

            if($response['totalresults'] == 0) {
                break;
            }

            if(isset($response['Result']['Id'])) {
                $keywords->push($response['Result']);
            }

            $keywords = $keywords->merge($response['Result']);

            if (!isset($response['nextpage'])) {
                break;
            }

        } while ($response['resultsreturned'] < $response['totalresults']);


        $keywords = $keywords->transform(function ($keyword, $key) {
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
        $arguments['keyword'] = implode(',', array_map(function ($el) {
                                    return str_replace(',', '\,', $el);
                                }, $keywords));

        if( ! is_null($tags) && count($tags) > 0)
            $arguments['tag'] = implode(',', $tags);

        if( ! is_null($location) && $location != '')
            $arguments['location'] = $location;

        $response = $this->performQuery('keywords/create', $arguments);

        $keywords = collect();

        if($response['resultsreturned'] == 0) {
            return $keywords;
        }
        if($response['resultsreturned'] == 1) {
            $keywords->push($response['Result']);
        } else {
            $keywords = collect($response['Result']);
        }

        return $keywords->transform(function ($keyword, $key) {
            return $this->transformCreatedKeyword($keyword);
        });

    }

    /**
     * @param int|array $id
     * @return Collection
     */
    public function delete($id)
    {
        if(!is_array($id)) {
            $id = [$id];
        }

        $ids = implode(',', $id);

        $response = $this->performQuery('keywords/delete', ['id' => $ids]);

        if(isset($response['Result']['Id'])){
            return collect($response['Result']['Id']);
        }


        return collect($response['Result'])->transform(function ($keywordID, $key) {
            return $keywordID['Id'];
        });

    }


    /**
     * @param $keyword
     * @return mixed
     */
    protected function transformCreatedKeyword($keyword) {
        $modifiedKeyword['id'] = (int)$keyword['Id'];
        $modifiedKeyword['keyword'] = $keyword['Keyword'];
        $modifiedKeyword['keyword_market'] = $keyword['KeywordMarket'];
        $modifiedKeyword['keyword_location'] = $keyword['KeywordLocation'];
        $modifiedKeyword['keyword_device'] = $keyword['KeywordDevice'];
        $modifiedKeyword['created_at'] = Carbon::parse($keyword['CreatedAt']);

        return $modifiedKeyword;
    }


    /**
     * @param $keyword
     * @return mixed
     */
    protected function transformListedKeyword($keyword) {
        $modifiedKeyword['id'] = (int)$keyword['Id'];
        $modifiedKeyword['keyword'] = $keyword['Keyword'];
        $modifiedKeyword['keyword_market'] = $keyword['KeywordMarket'];
        $modifiedKeyword['keyword_location'] = $keyword['KeywordLocation'];
        $modifiedKeyword['keyword_device'] = $keyword['KeywordDevice'];

        if($keyword['KeywordTags'] == 'none') {
            $modifiedKeyword['keyword_tags'] = collect();
        } else {
            $modifiedKeyword['keyword_tags'] = collect(explode(',', $keyword['KeywordTags']));
        }

        if( is_null($keyword['KeywordStats']) ) {
            $modifiedKeyword['keyword_stats'] = null;
        } else {
            $modifiedKeyword['keyword_stats']['advertiser_competition'] = (float)$keyword['KeywordStats']['AdvertiserCompetition'];
            $modifiedKeyword['keyword_stats']['global_search_volume'] = (int)$keyword['KeywordStats']['GlobalSearchVolume'];
            $modifiedKeyword['keyword_stats']['regional_search_volume'] = (int)$keyword['KeywordStats']['RegionalSearchVolume'];

            foreach ($keyword['KeywordStats']['LocalSearchTrendsByMonth'] as $month => $searchVolume) {
                if($searchVolume == '-') {
                    $searchVolume = '';
                } else {
                    $searchVolume = (int)$searchVolume;
                }
                $modifiedKeyword['keyword_stats']['local_search_trends_by_month'][strtolower($month)] = $searchVolume;
            }

            $modifiedKeyword['keyword_stats']['cpc'] = $keyword['KeywordStats']['CPC'];
        }

        if( is_null($keyword['KeywordRanking']) ) {
            $modifiedKeyword['keyword_ranking'] = null;
        } else {
            $modifiedKeyword['keyword_ranking']['date'] = Carbon::parse($keyword['KeywordRanking']['date']);
            $modifiedKeyword['keyword_ranking']['google'] = [
                'rank' => (int)$keyword['KeywordRanking']['Google']['Rank'],
                'base_rank' => (int)$keyword['KeywordRanking']['Google']['BaseRank'],
                'url' => $keyword['KeywordRanking']['Google']['Url'],
            ];
            $modifiedKeyword['keyword_ranking']['yahoo'] = [
                'rank' => (int)$keyword['KeywordRanking']['Yahoo']['Rank'],
                'url' => $keyword['KeywordRanking']['Yahoo']['Url'],
            ];
            $modifiedKeyword['keyword_ranking']['bing'] = [
                'rank' => (int)$keyword['KeywordRanking']['Bing']['Rank'],
                'url' => $keyword['KeywordRanking']['Bing']['Url'],
            ];
        }

        $modifiedKeyword['created_at'] = Carbon::parse($keyword['CreatedAt']);

        return $modifiedKeyword;
    }




}
