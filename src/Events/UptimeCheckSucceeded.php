<?php

namespace achegedus\UptimeMonitor\Events;

use achegedus\UptimeMonitor\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;

class UptimeCheckSucceeded implements ShouldQueue
{
    /** @var \achegedus\UptimeMonitor\Models\Monitor */
    public $monitor;

    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }
}
