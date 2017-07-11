# Anexia Monitoring

A Laravel package used to monitor updates for core and composer packages. It can be also used to check if the website
is alive and working correctly.

## Installation and configuration

Install the module via composer, therefore adapt the "require" part of your composer.json:
```
"require": {
        "anexia/laravel-monitoring": "1.0"
    },
```

To automatically publish the composer packages config files to the app, add the scripts to your composer.json:
```
"scripts": {
    "post-install-cmd": [
        "php artisan vendor:publish --provider=\"Anexia\\Monitoring\\Providers\\MonitoringServiceProvider\""
    ],
    "post-update-cmd": [
        "php artisan vendor:publish --provider=\"Anexia\\Monitoring\\Providers\\MonitoringServiceProvider\""
    ]
}
```

To manually add the anexia/laravel-monitoring's config file instead either run
```
php artisan vendor:publish --provider="Anexia\Monitoring\Providers\MonitoringServiceProvider"
```

sor manually copy the /vendor/anexia/laravel-monitoring/config/monitoring.php to
/app/config/monitoring.php after the composer update command.



In the projects config/app.php add the new service providers:
```
return [
    'providers' => [        
        /*
         * Anexia Monitoring Service Providers...
         */
        Anexia\Monitoring\MonitoringServiceProvider::class,
    ]
];
```

Now run
```
composer update [-o]
```
to add the packages source code to your /vendor directory and its config files to your /config directory.


In the projects .env config file add the access token configuration:
```
ANX_MONITORING_ACCESS_TOKEN=custom_monitoring_token
```


In the projects .env config file add the database connection configuration:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

### Custom DB Check for UpMonitoring (LiveMonitoring)

The anexia/laravel-monitoring only checks the db connection / db availability.
To add further db validation a customized helper class can be defined. This class must implement the 
Anexia\Monitoring\UpMonitoringInterface and must be callable via 'App\Helper\AnexiaMonitoringUpCheckHelper'.

Add a new helper class to the project tree as /app/Helper/AnexiaMonitoringUpCheckHelper, e.g.:
```
<?php
namespace App\Helper;

use Anexia\Monitoring\UpMonitoringInterface;

class AnexiaMonitoringUpCheckHelper implements UpMonitoringInterface
{
    /**
     * Check the db according to your requirements
     *
     * @return bool
     */
    public function checkUpStatus(&$errors = array())
    {
        // add db check/validation here
        /**
         * e.g.:
         *
         * if ($success) {
         *     return true;
         * } else {
         *     $errors[] = 'Database failure: something went wrong!';
         *     return false;
         * } 
         */
    }
}
```

The customized helper's 'checkUpStatus' method is automatically added to the anexia/laravel-monitoring package's db
check. If the customized helper's 'checkUpStatus' method returns false and/or adds content to its $error array 
(given as method parameter by reference), the anexia/laravel-monitoring package's db check will fail. 
If the customized helper's 'checkUpStatus' method returns false without giving any additional information in the $error
array (array stays empty), the default error message 'Database failure: custom check was not successful!' will be added
to the response. 

## Usage

The package registers some custom REST endpoints which can be used for monitoring. Make sure that the
**ANX_MONITORING_ACCESS_TOKEN** is defined, since this is used for authorization. The endpoints will return a 401
HTTP_STATUS code if the token is not defined or invalid, and a 200.

#### Version monitoring of core and composer packages

Returns all a list with platform and composer package information.

**URL:** `/anxapi/v1/modules/?access_token=custom_access_token`

Response headers:
```
Status Code: 200 OK
Access-Control-Allow-Origin: *
Access-Control-Allow-Credentials: true
Allow: GET
Content-Type: application/json
```

Response body:
```
{
   "runtime":{
      "platform":"php",
      "platform_version":"7.0.19",
      "framework":"laravel",
      "framework_installed_version":"5.4.28",
      "framework_newest_version":"3.0.1"
   },
   "modules":[
      {
         "name":"package-1",
         "installed_version":"3.1.10",
         "newest_version":"3.3.2"
      },
      {
         "name":"package-2",
         "installed_version":"1.4",
         "newest_version":"1.4"
      },
      ...
   ]
}
```


#### Live monitoring

This endpoint can be used to verify if the application is alive and working correctly. It checks if the database
connection is working and makes a query for users. It allows to register custom check by using hooks.

**URL:** `/anxapi/v1/up/?access_token=custom_access_token`

Response headers:
```
Status Code: 200 OK
Access-Control-Allow-Origin: *
Access-Control-Allow-Credentials: true
Allow: GET
Content-Type: text/plain
```

Response body:
```
OK
```


**Custom DB Check Failure (no custom error message)**
Response headers (custom check failed without additional error message):
```
Status Code: 500 Internal Server Error
Access-Control-Allow-Origin: *
Access-Control-Allow-Credentials: true
Allow: GET
Content-Type: text/plain
```

Response body (containing default error message):
```
Database failure: something went wrong!
```

**Custom DB Check Failure (custom error message)**
Response headers (custom check failed without additional error message):
```
Status Code: 500 Internal Server Error
Access-Control-Allow-Origin: *
Access-Control-Allow-Credentials: true
Allow: GET
Content-Type: text/plain
```

Response body (containing custom error message):
```
This is an example for a custom db check error message!
```


## List of developers

* Alexandra Bruckner, Lead developer

## Project related external resources

* [Laravel 5 documentation](https://laravel.com/docs/5.4/installation)
