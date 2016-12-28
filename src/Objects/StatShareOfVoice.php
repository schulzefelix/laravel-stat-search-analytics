<?php namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatShareOfVoice extends DataTransferObject
{
    protected $casts = [
        'date' => 'date',
    ];

}
