<?php namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatSubAccount extends DataTransferObject
{
    protected $casts = [
        'id' => 'integer',
    ];
}
