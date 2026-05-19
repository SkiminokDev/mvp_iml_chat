<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	return view('welcome');
});

// Публичный маршрут - доступен всем
Route::get('/ping', function() {
	return 'pong';
});

// Защищенный маршрут через стандартную веб-аутентификацию
//Route::middleware(['auth'])->get('/secure', function() {
//	return 'secure ok';
//});

//require __DIR__.'/auth.php';
//require __DIR__.'/admin.php';
//require __DIR__.'/public.php';
//require __DIR__.'/api.php';
