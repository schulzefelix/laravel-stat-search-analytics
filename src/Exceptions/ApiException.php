<?php

namespace SchulzeFelix\Stat\Exceptions;

use Exception;

class ApiException extends Exception
{
    public static function apiResultError($message = '')
    {
        return new static($message);
    }
}