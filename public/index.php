<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Standard Laravel: `public` lives inside the project (.. = app root).
// Hostinger-style: only `public` contents are in public_html; app is a sibling folder `elearn`.
$appRoot = file_exists(dirname(__DIR__).'/elearn/vendor/autoload.php')
    ? dirname(__DIR__).'/elearn'
    : dirname(__DIR__);

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $appRoot.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $appRoot.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $appRoot.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
