<?php

namespace achegedus\UptimeMonitor\Commands;

use achegedus\UptimeMonitor\Models\Monitor;
use achegedus\UptimeMonitor\MonitorRepository;
use achegedus\UptimeMonitor\Models\Enums\CertificateStatus;

class CheckCertificates extends BaseCommand
{
    protected $signature = 'monitor:check-certificate
                           {--url= : Only check these urls}';

    protected $description = 'Check the certificates of all sites';

    public function handle()
    {
        $monitors = MonitorRepository::getForCertificateCheck();

        if ($url = $this->option('url')) {
            $monitors = $monitors->filter(function (Monitor $monitor) use ($url) {
                return in_array((string) $monitor->url, explode(',', $url));
            });
        }

        $this->comment('Start checking the certificates of '.count($monitors).' monitors...');

        $monitors->each(function (Monitor $monitor) {
            $this->info("Checking certificate of {$monitor->url}");

            $monitor->checkCertificate();

            if ($monitor->certificate_status !== CertificateStatus::VALID) {
                $this->error("Could not download certificate of {$monitor->url} because: {$monitor->certificate_check_failure_reason}");
            }
        });

        $this->info('All done!');
    }
}
