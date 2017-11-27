<?php

namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatLocalSearchTrend extends DataTransferObject
{
    protected $casts = [
        'search_volume' => 'integer',
    ];

    public function setSearchVolumeAttribute($value)
    {
        return $this->attributes['search_volume'] = (int) $value;
    }
}
