<?php
use App\Http\Controllers\Admin\ChatAdminController;
use App\Http\Controllers\Admin\AuthorController;
// Группа админ-панели (префикс /admin)
Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {

	// Страница списка чатов
	Route::get('/chats', [ChatAdminController::class, 'index'])->name('chats.index');

	// Страница создания чата
	Route::get('/chats/create', [ChatAdminController::class, 'create'])->name('chats.create');

	// Обработка формы создания
	Route::post('/chats', [ChatAdminController::class, 'store'])->name('chats.store');
});

Route::prefix('admin')
	->name('admin.')
	->middleware(['auth']) // теперь 'login' существует, ошибка уйдёт
	->group(function () {
		Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
		
		// Роуты для управления авторами сообщений
		Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
		Route::get('/authors/{authorId}/messages', [AuthorController::class, 'messages'])->name('authors.messages');
});
