<?php

namespace achegedus\UptimeMonitor\Commands\MonitorLists;

use achegedus\UptimeMonitor\Models\Monitor;
use achegedus\UptimeMonitor\MonitorRepository;
use achegedus\UptimeMonitor\Helpers\ConsoleOutput;

class Disabled
{
    public static function display()
    {
        $disabledMonitors = MonitorRepository::getDisabled();

        if (! $disabledMonitors->count()) {
            return;
        }

        ConsoleOutput::warn('Disabled monitors');
        ConsoleOutput::warn('=================');

        $rows = $disabledMonitors->map(function (Monitor $monitor) {
            $url = $monitor->url;

            return compact('url');
        });

        $titles = ['URL'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
