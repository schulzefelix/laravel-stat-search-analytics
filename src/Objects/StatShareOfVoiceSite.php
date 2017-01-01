<?php namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatShareOfVoiceSite extends DataTransferObject
{
    protected $casts = [
        'share' => 'float',
        'pinned' => 'boolean'
    ];
}
