<?php namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class Stattag extends DataTransferObject
{
    protected $casts = [
        'id' => 'integer',
        'keywords' => 'collection',
    ];
}