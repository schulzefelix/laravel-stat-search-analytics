<?php

namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatTag extends DataTransferObject
{
    protected $casts = [
        'id' => 'integer',
        'keywords' => 'collection',
    ];
}
