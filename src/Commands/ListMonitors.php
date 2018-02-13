<?php

namespace achegedus\UptimeMonitor\Commands;

use achegedus\UptimeMonitor\MonitorRepository;
use achegedus\UptimeMonitor\Commands\MonitorLists\Healthy;
use achegedus\UptimeMonitor\Commands\MonitorLists\Disabled;
use achegedus\UptimeMonitor\Commands\MonitorLists\Unchecked;
use achegedus\UptimeMonitor\Commands\MonitorLists\UptimeCheckFailed;
use achegedus\UptimeMonitor\Commands\MonitorLists\CertificateCheckFailed;

class ListMonitors extends BaseCommand
{
    protected $signature = 'monitor:list';

    protected $description = 'List all monitors';

    public function handle()
    {
        $this->line('');

        if (! MonitorRepository::getEnabled()->count()) {
            $this->warn('There are no monitors created or enabled.');
            $this->info('You create a monitor using the `monitor:create {url}` command');
        }

        Unchecked::display();
        Disabled::display();
        UptimeCheckFailed::display();
        CertificateCheckFailed::display();
        Healthy::display();
    }
}
