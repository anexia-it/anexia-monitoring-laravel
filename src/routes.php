<?php

Route::get('/anxapi/v1/modules',  'Anexia\Monitoring\VersionMonitoringController@index');

Route::get('/anxapi/v1/up',  'Anexia\Monitoring\UpMonitoringController@index');