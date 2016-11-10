<?php

namespace SchulzeFelix\Stat\Exceptions;

use Exception;

class ApiException extends Exception
{
    public static function resultError($message = '')
    {
        return new static($message);
    }

    public static function requestException($message = '')
    {
        return new static($message);
    }
}
