<?php

namespace SchulzeFelix\Stat\Objects;

use SchulzeFelix\DataTransferObject\DataTransferObject;

class StatBillOptionalServiceType extends DataTransferObject
{
    protected $casts = [
        'count' => 'integer',
        'price' => 'float',
        'total' => 'float',
    ];
}
