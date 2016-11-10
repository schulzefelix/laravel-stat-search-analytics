<?php

namespace SchulzeFelix\Stat\Api;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class StatKeywords extends BaseStat
{


    public function list($siteID) : Collection
    {
        $start = 0;
        $keywords = collect();
        $x = 0;
        do {
            $response = $this->performQuery('keywords/list', ['site_id' => $siteID, 'start' => $start, 'results' => 2 ]);
            $start += 2;

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

            $x++; if($x > 3) { die('ddd');}
        } while ($response['resultsreturned'] < $response['totalresults']);

        return $keywords;

    }






}
