<?php

namespace achegedus\UptimeMonitor;

use Illuminate\Support\ServiceProvider;
use achegedus\UptimeMonitor\Commands\SyncFile;
use achegedus\UptimeMonitor\Commands\CheckUptime;
use achegedus\UptimeMonitor\Commands\ListMonitors;
use achegedus\UptimeMonitor\Commands\CreateMonitor;
use achegedus\UptimeMonitor\Commands\DeleteMonitor;
use achegedus\UptimeMonitor\Commands\EnableMonitor;
use achegedus\UptimeMonitor\Commands\DisableMonitor;
use achegedus\UptimeMonitor\Commands\CheckCertificates;
use achegedus\UptimeMonitor\Notifications\EventHandler;
use achegedus\UptimeMonitor\Helpers\UptimeResponseCheckers\UptimeResponseChecker;

class UptimeMonitorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            $this->publishes([
                __DIR__.'/../config/uptime-monitor.php' => config_path('uptime-monitor.php'),
            ], 'config');
        }

        if (! class_exists('CreateMonitorsTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../database/migrations/create_monitors_table.php.stub' => database_path('migrations/'.$timestamp.'_create_monitors_table.php'),
            ], 'migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/uptime-monitor.php', 'uptime-monitor');

        $this->app['events']->subscribe(EventHandler::class);

        $this->app->bind('command.monitor:check-uptime', CheckUptime::class);
        $this->app->bind('command.monitor:check-certificate', CheckCertificates::class);
        $this->app->bind('command.monitor:sync-file', SyncFile::class);
        $this->app->bind('command.monitor:create', CreateMonitor::class);
        $this->app->bind('command.monitor:delete', DeleteMonitor::class);
        $this->app->bind('command.monitor:enable', EnableMonitor::class);
        $this->app->bind('command.monitor:disable', DisableMonitor::class);
        $this->app->bind('command.monitor:list', ListMonitors::class);

        $this->app->bind(
            UptimeResponseChecker::class,
            config('uptime-monitor.uptime_check.response_checker')
        );

        $this->commands([
            'command.monitor:check-uptime',
            'command.monitor:check-certificate',
            'command.monitor:sync-file',
            'command.monitor:create',
            'command.monitor:delete',
            'command.monitor:enable',
            'command.monitor:disable',
            'command.monitor:list',
        ]);
    }
}
