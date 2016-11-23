<?php namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatEngineRankDistribution extends DataTransferObject
{
    protected $casts = [
        'one' => 'integer',
        'two' => 'integer',
        'three' => 'integer',
        'four' => 'integer',
        'five' => 'integer',
        'six_to_ten' => 'integer',
        'eleven_to_twenty' => 'integer',
        'twenty_one_to_thirty' => 'integer',
        'thirty_one_to_forty' => 'integer',
        'forty_one_to_fifty' => 'integer',
        'fifty_one_to_hundred' => 'integer',
        'non_ranking' => 'integer',
    ];
}