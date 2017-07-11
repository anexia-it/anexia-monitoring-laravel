<?php
namespace Anexia\Monitoring\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class MonitoringServiceProvider
 * @package Anexia\Monitoring\Providers
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
        // add additional config files
        $this->publishes([
            __DIR__ . '/../../config/monitoring.php' => $this->app['path.config'] . DIRECTORY_SEPARATOR . 'monitoring.php',
        ], 'anexia-monitoring');
    }

    /**
     * Register the application services
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . '/../routes.php';
        $this->app->make('Anexia\Monitoring\Controllers\VersionMonitoringController');
        $this->app->make('Anexia\Monitoring\Controllers\UpMonitoringController');

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/monitoring.php', 'monitoring'
        );
    }
}