<?php namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatBillSummary extends DataTransferObject
{
    protected $casts = [
        'min_committed_charge' => 'float',
        'tracked_keywords' => 'integer',
        'tracked_keywords_total' => 'float',
        'optional_service_total' => 'float',
        'total' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $dates = [
        'start_date',
        'end_date'
    ];
}
