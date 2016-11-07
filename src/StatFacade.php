<?php

namespace SchulzeFelix\Stat;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SchulzeFelix\Stat\Stat
 */
class StatFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-stat-search-analytics';
    }
}