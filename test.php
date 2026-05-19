<?php
// routes/test.php
use Illuminate\Support\Facades\Route;

Route::get('/ping', function() {
	return 'pong';
});

Route::middleware('auth:sanctum')->get('/secure', function() {
	return 'secure ok';
});
