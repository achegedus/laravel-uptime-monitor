<?php

namespace achegedus\UptimeMonitor\Commands\MonitorLists;

use achegedus\UptimeMonitor\Models\Monitor;
use achegedus\UptimeMonitor\MonitorRepository;
use achegedus\UptimeMonitor\Helpers\ConsoleOutput;

class Unchecked
{
    public static function display()
    {
        $uncheckedMonitors = MonitorRepository::getUnchecked();

        if (! $uncheckedMonitors->count()) {
            return;
        }

        ConsoleOutput::warn('Not yet checked');
        ConsoleOutput::warn('===============');

        $rows = $uncheckedMonitors->map(function (Monitor $monitor) {
            $url = $monitor->url;

            return compact('url');
        });

        $titles = ['URL'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
