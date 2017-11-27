<?php

namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatSerpItem extends DataTransferObject
{
    protected $casts = [
        'rank' => 'integer',
        'base_rank' => 'integer',
    ];
}
