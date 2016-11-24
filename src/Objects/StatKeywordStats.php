<?php namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatKeywordStats extends DataTransferObject
{
    protected $casts = [
        'advertiser_competition' => 'float',
        'global_search_volume' => 'integer',
        'regional_search_volume' => 'integer',
        'targeted_search_volume' => 'integer',
        'local_search_trends_by_month' => 'collection',
        'cpc' => 'float',
    ];
}
