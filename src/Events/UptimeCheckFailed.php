<?php

namespace achegedus\UptimeMonitor\Events;

use achegedus\UptimeMonitor\Helpers\Period;
use achegedus\UptimeMonitor\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;

class UptimeCheckFailed implements ShouldQueue
{
    /** @var \achegedus\UptimeMonitor\Models\Monitor */
    public $monitor;
    /**
     * @var \achegedus\UptimeMonitor\Helpers\Period
     */
    public $downtimePeriod;

    public function __construct(Monitor $monitor, Period $downtimePeriod)
    {
        $this->monitor = $monitor;

        $this->downtimePeriod = $downtimePeriod;
    }
}
