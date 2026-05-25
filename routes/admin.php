<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ChatAdminController;
use App\Http\Controllers\Admin\AuthorController;


Route::middleware(['web']) // Всегда добавляйте 'web' для работы сессий/авторизации
->group(function () {

	// Чаты
	Route::get('/chats', [ChatAdminController::class, 'index'])->name('chats.index');
	Route::get('/chats/create', [ChatAdminController::class, 'create'])->name('chats.create');
	Route::post('/chats', [ChatAdminController::class, 'store'])->name('chats.store');

	// Пользователи (resource)
	Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

	// Авторы
	Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
	Route::get('/authors/{authorId}/messages', [AuthorController::class, 'messages'])->name('authors.messages');
});