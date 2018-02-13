<?php

namespace achegedus\UptimeMonitor\Events;

use achegedus\UptimeMonitor\Models\Monitor;
use Spatie\SslCertificate\SslCertificate;
use Illuminate\Contracts\Queue\ShouldQueue;

class CertificateExpiresSoon implements ShouldQueue
{
    /** @var \achegedus\UptimeMonitor\Models\Monitor */
    public $monitor;

    /** @var \Spatie\SslCertificate\SslCertificate */
    public $certificate;

    public function __construct(Monitor $monitor, SslCertificate $certificate)
    {
        $this->monitor = $monitor;

        $this->certificate = $certificate;
    }
}
