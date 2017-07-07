<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Monitoring
    |--------------------------------------------------------------------------
    |
    | This section defines the variables needed for the
    | anexia-laravel-monitoring project. It should not be necessary to change
    | anything here.
    |
    */

    'access_token' => env('ANX_MONITORING_ACCESS_TOKEN'),

    'table_to_check' => env('ANX_MONITORING_TABLE_TO_CHECK', \Anexia\Monitoring\UpMonitoringController::DEFAULT_TABLE_TO_CHECK),

];
