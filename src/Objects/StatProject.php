<?php

namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatProject extends DataTransferObject
{
    protected $casts = [
        'id' => 'integer',
        'total_sites' => 'integer',
    ];
}
