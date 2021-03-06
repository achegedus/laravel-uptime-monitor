<?php

namespace achegedus\UptimeMonitor\Models\Presenters;

use achegedus\UptimeMonitor\Models\Enums\UptimeStatus;
use achegedus\UptimeMonitor\Models\Enums\CertificateStatus;

trait MonitorPresenter
{
    public function getUptimeStatusAsEmojiAttribute(): string
    {
        if ($this->uptime_status === UptimeStatus::UP) {
            return '✅';
        }

        if ($this->uptime_status === UptimeStatus::DOWN) {
            return '❌';
        }

        return '';
    }

    public function getCertificateStatusAsEmojiAttribute(): string
    {
        if ($this->certificate_status === CertificateStatus::VALID) {
            return '✅';
        }

        if ($this->certificate_status === CertificateStatus::INVALID) {
            return '❌';
        }

        return '';
    }

    public function formattedLastUpdatedStatusChangeDate(string $format = ''): string
    {
        return $this->formatDate('uptime_status_last_change_date', $format);
    }

    public function formattedCertificateExpirationDate(string $format = ''): string
    {
        return $this->formatDate('certificate_expiration_date', $format);
    }

    public function getChunkedLastFailureReasonAttribute(): string
    {
        if ($this->uptime_check_failure_reason == '') {
            return '';
        }

        return chunk_split($this->uptime_check_failure_reason, 30, "\n");
    }

    public function getChunkedLastCertificateCheckFailureReasonAttribute(): string
    {
        if ($this->certificate_check_failure_reason == '') {
            return '';
        }

        return chunk_split($this->certificate_check_failure_reason, 60, "\n");
    }

    protected function formatDate(string $attributeName, string $format = ''): string
    {
        if (! $this->$attributeName) {
            return '';
        }

        if ($format === 'forHumans') {
            return $this->$attributeName->diffForHumans();
        }

        if ($format === '') {
            $format = config('uptime-monitor.notifications.date_format');
        }

        return $this->$attributeName->format($format);
    }
}
