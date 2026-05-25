<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	return view('welcome');
});

// Публичный маршрут - доступен всем
Route::get('/ping', function() {
	return 'pong';
});

Route::prefix('admin')
	->name('admin.')
	->middleware(config('api.auth_required') ? ['web'] : []) //'web'
	->group(base_path('routes/admin.php'));

require __DIR__.'/auth.php';
// Защищенный маршрут через стандартную веб-аутентификацию
//Route::middleware(['auth'])->get('/secure', function() {
//	return 'secure ok';
//});

//require __DIR__.'/auth.php';
//require __DIR__.'/admin.php';
//require __DIR__.'/public.php';
//require __DIR__.'/api.php';
