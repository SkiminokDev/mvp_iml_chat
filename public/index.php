<?php
// public/index.php - после <?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Добавьте отладку памяти
register_shutdown_function(function() {
	$error = error_get_last();
	if ($error) {
		file_put_contents(__DIR__.'/../storage/logs/fatal_error.log',
			date('Y-m-d H:i:s') . ' - ' . print_r($error, true) . "\n",
			FILE_APPEND);
	}
});
//use Illuminate\Foundation\Application;
//use Illuminate\Http\Request;
//
//define('LARAVEL_START', microtime(true));
//
//// Determine if the application is in maintenance mode...
//if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
//    require $maintenance;
//}
//
//// Register the Composer autoloader...
//require __DIR__.'/../vendor/autoload.php';
//
//// Bootstrap Laravel and handle the request...
///** @var Application $app */
//$app = require_once __DIR__.'/../bootstrap/app.php';
//
//$app->handleRequest(Request::capture());
