<?php

namespace achegedus\UptimeMonitor\Commands;

use achegedus\UptimeMonitor\Models\Monitor;
use achegedus\UptimeMonitor\MonitorRepository;

class CheckUptime extends BaseCommand
{
    protected $signature = 'monitor:check-uptime  
                            {--url= : Only check these urls}';

    protected $description = 'Check the uptime of all sites';

    public function handle()
    {
        $monitors = MonitorRepository::getForUptimeCheck();

        if ($url = $this->option('url')) {
            $monitors = $monitors->filter(function (Monitor $monitor) use ($url) {
                return in_array((string) $monitor->url, explode(',', $url));
            });
        }

        $this->comment('Start checking the uptime of '.count($monitors).' monitors...');

        $monitors->checkUptime();

        $this->info('All done!');
    }
}
