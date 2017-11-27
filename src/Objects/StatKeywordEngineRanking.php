<?php

namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatKeywordEngineRanking extends DataTransferObject
{
    protected $cast = [
        'rank' => 'integer',
        'base_rank' => 'integer',
    ];
}
