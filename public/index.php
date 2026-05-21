<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Включаем отображение ошибок (для разработки)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Регистрация обработчика фатальных ошибок
register_shutdown_function(function() {
	$error = error_get_last();
	if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
		file_put_contents(
			__DIR__.'/../storage/logs/fatal_error.log',
			date('Y-m-d H:i:s') . ' - ' . print_r($error, true) . "\n",
			FILE_APPEND
		);
	}
});

// Проверка режима обслуживания
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
	require $maintenance;
}

// Подключение автолоадера Composer
require __DIR__.'/../vendor/autoload.php';

// Загрузка приложения Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Обработка запроса
$app->handleRequest(Request::capture());