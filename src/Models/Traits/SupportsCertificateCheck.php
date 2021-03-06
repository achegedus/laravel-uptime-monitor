<?php

namespace achegedus\UptimeMonitor\Models\Traits;

use Exception;
use achegedus\UptimeMonitor\Models\Monitor;
use Spatie\SslCertificate\SslCertificate;
use achegedus\UptimeMonitor\Events\CertificateCheckFailed;
use achegedus\UptimeMonitor\Events\CertificateExpiresSoon;
use achegedus\UptimeMonitor\Models\Enums\CertificateStatus;
use achegedus\UptimeMonitor\Events\CertificateCheckSucceeded;
use achegedus\SslCertificate\Exceptions\CouldNotDownloadCertificate;

trait SupportsCertificateCheck
{
    public function checkCertificate()
    {
        try {
            $certificate = SslCertificate::createForHostName($this->url->getHost());

            $this->setCertificate($certificate);
        } catch (CouldNotDownloadCertificate $exception) {
            $this->setCertificateException($exception);
        }
    }

    public function setCertificate(SslCertificate $certificate)
    {
        $this->certificate_status = $certificate->isValid($this->url)
            ? CertificateStatus::VALID
            : CertificateStatus::INVALID;

        $this->certificate_expiration_date = $certificate->expirationDate();
        $this->certificate_issuer = $certificate->getIssuer();
        $this->save();

        $this->fireEventsForUpdatedMonitorWithCertificate($this, $certificate);
    }

    public function setCertificateException(Exception $exception)
    {
        $this->certificate_status = CertificateStatus::INVALID;
        $this->certificate_expiration_date = null;
        $this->certificate_issuer = '';
        $this->certificate_check_failure_reason = $exception->getMessage();
        $this->save();

        event(new CertificateCheckFailed($this, $exception->getMessage()));
    }

    protected function fireEventsForUpdatedMonitorWithCertificate(Monitor $monitor, SslCertificate $certificate)
    {
        if ($this->certificate_status === CertificateStatus::VALID) {
            event(new CertificateCheckSucceeded($this, $certificate));

            if ($certificate->expirationDate()->diffInDays() <= config('uptime-monitor.certificate_check.fire_expiring_soon_event_if_certificate_expires_within_days')) {
                event(new CertificateExpiresSoon($monitor, $certificate));
            }

            return;
        }

        if ($this->certificate_status === CertificateStatus::INVALID) {
            $reason = 'Unknown';

            if ($certificate->appliesToUrl($this->url)) {
                $reason = "Certificate does not apply to {$this->url} but only to these domains: ".implode(',', $certificate->getAdditionalDomains());
            }

            if ($certificate->isExpired()) {
                $reason = 'The certificate has expired';
            }

            $this->certificate_check_failure_reason = $reason;
            $this->save();

            event(new CertificateCheckFailed($this, $reason, $certificate));
        }
    }
}
