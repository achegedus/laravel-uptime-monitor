<?php

namespace achegedus\UptimeMonitor\Commands\MonitorLists;

use achegedus\UptimeMonitor\Models\Monitor;
use achegedus\UptimeMonitor\MonitorRepository;
use achegedus\UptimeMonitor\Helpers\ConsoleOutput;

class UptimeCheckFailed
{
    public static function display()
    {
        $failingMonitors = MonitorRepository::getWithFailingUptimeCheck();

        if (! $failingMonitors->count()) {
            return;
        }

        ConsoleOutput::warn('Uptime check failed');
        ConsoleOutput::warn('===================');

        $rows = $failingMonitors->map(function (Monitor $monitor) {
            $url = $monitor->url;

            $reachable = $monitor->uptimeStatusAsEmoji;

            $offlineSince = $monitor->formattedLastUpdatedStatusChangeDate('forHumans');

            $reason = $monitor->chunkedLastFailureReason;

            if ($monitor->certificate_check_enabled) {
                $certificateFound = $monitor->certificateStatusAsEmoji;
                $certificateExpirationDate = $monitor->formattedCertificateExpirationDate('forHumans');
                $certificateIssuer = $monitor->certificate_issuer;
            }

            return compact('url', 'reachable', 'offlineSince', 'reason', 'certificateFound', 'certificateExpirationDate', 'certificateIssuer');
        });

        $titles = ['URL', 'Reachable', 'Offline since', 'Reason', 'Certificate', 'Certificate expiration date', 'Certificate issuer'];

        ConsoleOutput::table($titles, $rows);
        ConsoleOutput::line('');
    }
}
