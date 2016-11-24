<?php namespace SchulzeFelix\Stat;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use SchulzeFelix\Stat\Api\StatBulk;
use SchulzeFelix\Stat\Api\StatKeywords;
use SchulzeFelix\Stat\Api\StatProjects;
use SchulzeFelix\Stat\Api\StatRankings;
use SchulzeFelix\Stat\Api\StatSerps;
use SchulzeFelix\Stat\Api\StatSites;
use SchulzeFelix\Stat\Api\StatSubAccounts;
use SchulzeFelix\Stat\Api\StatTags;

class Stat
{

    /**
     * @var StatClient
     */
    private $statClient;


    /**
     * Stat constructor.
     */
    public function __construct(StatClient $statClient)
    {
        $this->statClient = $statClient;
    }

    public function projects()
    {
        return new StatProjects($this->statClient);
    }

    public function sites()
    {
        return new StatSites($this->statClient);
    }

    public function tags()
    {
        return new StatTags($this->statClient);
    }

    public function keywords()
    {
        return new StatKeywords($this->statClient);
    }

    public function rankings()
    {
        return new StatRankings($this->statClient);
    }

    public function serps()
    {
        return new StatSerps($this->statClient);
    }

    public function bulk()
    {
        return new StatBulk($this->statClient);
    }

    public function subaccounts()
    {
        return new StatSubAccounts($this->statClient);
    }

    public function blockedUntil()
    {
        try {
            $this->statClient->performQuery('projects/list', []);
        } catch (ClientException $e) {
            $now = Carbon::now();
            try {
                if ($e->getCode() == 403) {
                    preg_match("/(\d{1,2}) hours and (\d{1,2}) minutes/", $e->getResponse()->getBody()->getContents(), $matches);
                    return $now->addHours($matches[1])->addMinutes($matches[2]);
                }
            } catch (\Exception $e) {
                //
            }
        }

        return Carbon::now();
    }
}
