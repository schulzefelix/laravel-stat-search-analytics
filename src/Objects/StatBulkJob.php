<?php

namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatBulkJob extends DataTransferObject
{
    protected $casts = [
        'id' => 'integer',
    ];

    protected $dates = [
        'date',
    ];
}
