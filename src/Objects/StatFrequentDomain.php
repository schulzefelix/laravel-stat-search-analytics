<?php

namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatFrequentDomain extends DataTransferObject
{
    protected $casts = [
        'top_ten_results' => 'integer',
        'results_analyzed' => 'integer',
        'coverage' => 'float',
        'analyzed_on' => 'date',
    ];

    public function setTopTenResultsAttribute($value)
    {
        return $this->attributes['top_ten_results'] = (int) $value;
    }

    public function setResultsAnalyzedAttribute($value)
    {
        return $this->attributes['results_analyzed'] = (int) $value;
    }

    public function setCoverageAttribute($value)
    {
        return $this->attributes['coverage'] = (float) $value;
    }
}
