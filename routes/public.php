<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\MessController;

Route::get('/', [HomeController::class, 'index']);

Route::middleware('auth')->prefix('user')->name('user.')->group(function () {
	Route::resource('chats', ChatController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
	Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('chats.show');
	// 📡 API: получение сообщений (для AJAX)
	Route::get('/chats/{chat}/messages', [ChatController::class, 'getMessages'])->name('chats.messages');
	// ✉️ AJAX: отправка сообщения (исправляем 419 и 404)
	Route::post('/messages/send', [MessController::class, 'send'])->name('messages.send');
});

Route::middleware('auth')->group(function () {
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', function () {
	return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
	Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
	Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('chats.show');
	Route::post('/chats/{chat}', [ChatController::class, 'show'])->name('chats.show');
});

Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
	// Отправка сообщения через MessController
	Route::post('/messages/send', [MessController::class, 'send'])->name('messages.send');

	// Получение сообщений через ChatController
	Route::get('/chats/{chat}/messages', [ChatController::class, 'getMessages'])->name('chats.messages');
});