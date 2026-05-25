<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\MessageController;

// ПРОСТЕЙШИЙ ТЕСТ - БЕЗ КОНТРОЛЛЕРОВ
Route::get('/test-ping', function() {
	return response()->json(['message' => 'ping ok']);
});

Route::middleware('auth:sanctum')->get('/test-me', function(Request $request) {
	$user = $request->user();
	return response()->json([
		'user_id' => $user->id,
		'email' => $user->email
	]);
});


// =============================================
// API v1 - Сообщения и разговоры
// =============================================
Route::middleware(config('api.auth_required') ? ['auth:sanctum'] : [])
	->prefix('v1')
	->name('api.v1.')
	->group(function () {
		// проверка запроса
		Route::get('/info', fn()=>response()->json(['version' => 'v1', 'dev'=>'llmBot']));
		// работа с сообщениями
		Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
	});


//use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\Api\MessController;
//use App\Http\Controllers\Api\ChatApiController;
//use Illuminate\Http\Request;
//
//// =============================================
//// ПУБЛИЧНЫЕ API МАРШРУТЫ (без аутентификации)
//// =============================================
//Route::prefix('v1')->group(function () {
//	// Статус API (публичный)
//	Route::get('/status', function () {
//		return response()->json([
//			'service' => 'Chat API',
//			'version' => '1.0',
//			'status' => 'online',
//			'timestamp' => now()->toIso8601String(),
//		]);
//	})->name('api.status');
//});
//
//
//// =============================================
//// ДИАГНОСТИЧЕСКИЕ МАРШРУТЫ (добавьте перед группой с auth:sanctum)
//// =============================================
//Route::get('/debug/token-info', function(Request $request) {
//	$authHeader = $request->header('Authorization');
//	$token = str_replace('Bearer ', '', $authHeader);
//
//	$accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
//
//	if (!$accessToken) {
//		return response()->json([
//			'error' => 'Token invalid',
//			'token_provided' => substr($token, 0, 20) . '...',
//			'format' => 'Should be: {id}|{hash}'
//		], 401);
//	}
//
//	return response()->json([
//		'success' => true,
//		'token_id' => $accessToken->id,
//		'user' => [
//			'id' => $accessToken->tokenable->id,
//			'email' => $accessToken->tokenable->email
//		]
//	]);
//})->middleware('auth:sanctum');
//
//Route::post('/debug/login', function(Request $request) {
//	$credentials = $request->only('email', 'password');
//
//	if (Auth::attempt($credentials)) {
//		$user = Auth::user();
//		$token = $user->createToken('debug-token');
//
//		return response()->json([
//			'success' => true,
//			'message' => 'Login successful',
//			'user' => [
//				'id' => $user->id,
//				'email' => $user->email,
//				'is_admin' => $user->is_admin,
//			],
//			'token' => $token->plainTextToken,
//		]);
//	}
//
//	return response()->json([
//		'success' => false,
//		'message' => 'Invalid credentials'
//	], 401);
//});
//
//Route::get('/debug/check-auth', function(Request $request) {
//	return response()->json([
//		'headers' => [
//			'authorization' => $request->header('authorization'),
//			'accept' => $request->header('accept'),
//			'content-type' => $request->header('content-type'),
//		],
//		'user_check' => [
//			'has_user' => !is_null($request->user()),
//			'user_id' => $request->user()?->id,
//			'user_email' => $request->user()?->email,
//		]
//	]);
//})->middleware('auth:sanctum');
//
//
//// =============================================
//// ЗАЩИЩЕННЫЕ API МАРШРУТЫ (с аутентификацией)
//// =============================================
//Route::middleware('auth:sanctum')->group(function () {
//
//	// API v1 - чаты и сообщения
//	Route::prefix('v1/chat')->name('api.v1.chat.')->group(function () {
//		// Отправка сообщения
//		Route::post('/messages', [MessController::class, 'send'])->name('messages.send');
//
//		// Получение истории чата
//		Route::get('/chats/{chat}/messages', [ChatApiController::class, 'getMessages'])->name('messages.list');
//
//		// Управление чатами
//		Route::get('/chats', [ChatApiController::class, 'index'])->name('chats.list');
//		Route::get('/chats/{chat}', [ChatApiController::class, 'show'])->name('chats.show');
//	});
//
//	// Альтернативный API (для обратной совместимости)
//	Route::prefix('api')->name('api.')->group(function () {
//		// Отправка сообщения
//		Route::post('/messages/send', [MessController::class, 'send'])->name('messages.send');
//
//		// Получение истории чата
//		Route::get('/chats/{chat}/messages', [ChatApiController::class, 'getMessages'])->name('chats.messages');
//
//		// Управление чатами
//		Route::get('/chats', [ChatApiController::class, 'index'])->name('chats.index');
//		Route::get('/chats/{chat}', [ChatApiController::class, 'show'])->name('chats.show');
//	});
//
//	// Управление токенами
//	Route::post('/user/tokens', function (Request $request) {
//		$token = $request->user()->createToken($request->name ?? 'api-token');
//		return response()->json(['token' => $token->plainTextToken]);
//	})->name('api.tokens.create');
//});