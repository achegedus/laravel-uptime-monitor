<?php

namespace achegedus\UptimeMonitor\Exceptions;

use Exception;
use achegedus\UptimeMonitor\Models\Monitor;

class InvalidConfiguration extends Exception
{
    public static function modelIsNotValid(string $className)
    {
        return new static("The given model class `{$className}` does not extend `".Monitor::class.'`');
    }
}
