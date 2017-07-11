=== Anexia Monitoring ===
Contributors: anxabruckner
License: MIT

A Laravel package used to monitor updates for core and composer packages. It can be also used to check if the website
is alive and working correctly.

== Description ==
A Laravel package used to monitor updates for core and composer packages. It can be also used to check if the website
is alive and working correctly.

The module registers some custom REST endpoints which can be used for monitoring. Make sure that the
**ANX_MONITORING_ACCESS_TOKEN** is defined, since this is used for authorization. The endpoints will return a 401
HTTP_STATUS code if the token is not defined or invalid, and a 200.

= Version monitoring of core and composer packages =

Returns all a list with platform and package information.

**Active permalinks**

	/anxapi/v1/modules/?access_token=custom_access_token

**Default**

	/?rest_route=/anxapi/v1/modules/&access_token=custom_access_token

= Live monitoring =

This endpoint can be used to verify if the application is alive and working correctly. It checks if the database
connection is working and makes a query for users. It allows to register custom check by using hooks.

**Active permalinks**

	/anxapi/v1/up/?access_token=custom_access_token

**Default**

	/?rest_route=/anxapi/v1/up/&access_token=custom_access_token

== Installation ==
In the projects local.php add the access token configuration:

	return array(
        'ANX_MONITORING_ACCESS_TOKEN' => '<custom_monitoring_token>'
    );


Install the module via composer, therefore adapt the "require" part of your composer.json:
    "require": {
            "anexia/laravel-monitoring": "1.0"
    },

To automatically publish the composer packages config files to the app, add the scripts to your composer.json:
    "scripts": {
        "post-install-cmd": [
            "php artisan vendor:publish --provider=\"Anexia\\Monitoring\\Providers\\MonitoringServiceProvider\""
        ],
        "post-update-cmd": [
            "php artisan vendor:publish --provider=\"Anexia\\Monitoring\\Providers\\MonitoringServiceProvider\""
        ]
    }
To manually add the anexia/laravel-monitoring's config file instead either run
    php artisan vendor:publish --provider="Anexia\Monitoring\Providers\MonitoringServiceProvider"
or manually copy the /vendor/anexia/laravel-monitoring/config/monitoring.php to
/app/config/monitoring.php after the composer update command.


In the projects config/app.php add the new service providers:
    return [
        'providers' => [
            /*
             * Anexia Monitoring Service Providers...
             */
            Anexia\Monitoring\MonitoringServiceProvider::class,
        ]
    ];

Now run
    composer update [-o]
to add the packages source code to your /vendor directory and its config files to your /config directory.



In the projects config/app.php add the new service providers:
    return [
        'providers' => [
            /*
             * Anexia Monitoring Service Providers...
             */
            Anexia\Monitoring\MonitoringServiceProvider::class,
        ]
    ];


In the projects .env config file add the access token configuration:
    ANX_MONITORING_ACCESS_TOKEN=custom_monitoring_token


In the projects .env config file add the database connection configuration:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=homestead
    DB_USERNAME=homestead
    DB_PASSWORD=secret