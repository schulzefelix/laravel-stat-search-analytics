<?php namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatBill extends DataTransferObject
{
    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
    ];
}
