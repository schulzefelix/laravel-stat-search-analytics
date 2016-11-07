<?php namespace SchulzeFelix\Stat;

use SchulzeFelix\Stat\Api\StatProjects;
use SchulzeFelix\Stat\Api\StatSites;

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


}