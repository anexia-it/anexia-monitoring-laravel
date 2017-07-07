# Anexia Monitoring

A Laravel package used to monitor updates for core, plugins and themes. It can be also used to check if the website
is alive and working correctly.

## Installation and configuration

Install the module via composer, therefore adapt the "require" part of your composer.json:
```
"require": {
        "anexia/laravel-monitoring": "1.0"
    },
```


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


In the projects .env config file add the database table to be checked on live (/up) monitoring:
```
ANX_MONITORING_TABLE_TO_CHECK=user
```

## Usage

The package registers some custom REST endpoints which can be used for monitoring. Make sure that the
**ANX_MONITORING_ACCESS_TOKEN** is defined, since this is used for authorization. The endpoints will return a 401
HTTP_STATUS code if the token is not defined or invalid, and a 200.

#### Version monitoring of core, plugins and themes

Returns all a list with platform and module information.

**URL:**
* Active permalinks: `/anxapi/v1/modules/?access_token=custom_access_token`
* Default: `/?rest_route=/anxapi/v1/modules/&access_token=custom_access_token`

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

**URL:**
* Active permalinks: `/anxapi/v1/up/?access_token=custom_access_token`
* Default: `/?rest_route=/anxapi/v1/up/&access_token=custom_access_token`

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


## List of developers

* Alexandra Bruckner, Lead developer

## Project related external resources

* [Laravel 5 documentation](https://laravel.com/docs/5.4/installation)