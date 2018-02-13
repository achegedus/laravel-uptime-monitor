<?php

namespace achegedus\UptimeMonitor\Notifications;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use achegedus\UptimeMonitor\Events\UptimeCheckFailed;
use achegedus\UptimeMonitor\Events\UptimeCheckRecovered;
use achegedus\UptimeMonitor\Events\UptimeCheckSucceeded;
use achegedus\UptimeMonitor\Events\CertificateCheckFailed;
use achegedus\UptimeMonitor\Events\CertificateExpiresSoon;
use achegedus\UptimeMonitor\Events\CertificateCheckSucceeded;

class EventHandler
{
    /** @var \Illuminate\Config\Repository */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen($this->allEventClasses(), function ($event) {
            $notification = $this->determineNotification($event);

            if (! $notification) {
                return;
            }

            if ($notification->isStillRelevant()) {
                $notifiable = $this->determineNotifiable();

                $notifiable->notify($notification);
            }
        });
    }

    protected function determineNotifiable()
    {
        $notifiableClass = $this->config->get('uptime-monitor.notifications.notifiable');

        return app($notifiableClass);
    }

    protected function determineNotification($event)
    {
        $eventName = class_basename($event);

        $notificationClass = collect($this->config->get('uptime-monitor.notifications.notifications'))
            ->filter(function (array $notificationChannels) {
                return count($notificationChannels);
            })
            ->keys()
            ->first(function ($notificationClass) use ($eventName) {
                $notificationName = class_basename($notificationClass);

                return $notificationName === $eventName;
            });

        if ($notificationClass) {
            return app($notificationClass)->setEvent($event);
        }
    }

    protected function allEventClasses(): array
    {
        return [
            UptimeCheckFailed::class,
            UptimeCheckSucceeded::class,
            UptimeCheckRecovered::class,
            CertificateCheckSucceeded::class,
            CertificateCheckFailed::class,
            CertificateExpiresSoon::class,
        ];
    }
}
