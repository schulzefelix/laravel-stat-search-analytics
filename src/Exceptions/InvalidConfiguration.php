<?php

namespace SchulzeFelix\Stat\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{

    public static function keyNotSpecified()
    {
        return new static('You must provide an API key.');
    }
}
