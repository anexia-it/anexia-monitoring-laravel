<?php

Route::get('/anxapi/v1/modules',  'Anexia\Monitoring\Controllres\VersionMonitoringController@index');

Route::get('/anxapi/v1/up',  'Anexia\Monitoring\Controllers\UpMonitoringController@index');