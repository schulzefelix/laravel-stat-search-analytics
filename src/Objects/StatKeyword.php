<?php namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatKeyword extends DataTransferObject
{
    protected $casts = [
        'id' => 'integer',
        'keyword_tags' => 'collection',
        'total_keywords' => 'integer',
        'drop_www_prefix' => 'boolean',
        'drop_directories' => 'boolean',
    ];
}
