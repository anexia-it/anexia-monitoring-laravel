<?php
namespace Anexia\Monitoring;

use Illuminate\Support\ServiceProvider;

/**
 * Class MonitoringServiceProvider
 * @package Anexia\Monitoring
 */
class MonitoringServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . '/routes.php';
        $this->app->make('Anexia\Monitoring\VersionMonitoringController');
        $this->app->make('Anexia\Monitoring\UpMonitoringController');

        // add additional config files
        $this->publishes([
            __DIR__ . '/../config/monitoring.php' => $this->app['path.config'] . DIRECTORY_SEPARATOR . 'monitoring.php',
        ]);
    }
}