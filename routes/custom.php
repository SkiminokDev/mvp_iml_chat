<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\MessController;
use App\Http\Controllers\Admin\ChatAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Здесь регистрируются все маршруты для вашего приложения.
| Маршруты загружаются RouteServiceProvider в группу,
| которая содержит middleware "web".
|
*/

// === 🏠 ПУБЛИЧНЫЕ СТРАНИЦЫ ===

// Главная страница
Route::get('/', [HomeController::class, 'index'])->name('home');

// Страница чата (доступна авторизованным пользователям)
Route::middleware(['auth'])->group(function() {
	Route::get('/chat', function() {
		return view('chat');
	})->name('chat');
});

// === 📡 API МАРШРУТЫ (AJAX) ===
Route::prefix('api')->name('api.')->group(function() {

	// Отправка сообщения в чат (требует авторизации)
	Route::middleware(['auth'])->group(function() {
		Route::post('/message', [MessController::class, 'send'])->name('message.send');
	});

	// Публичные API-эндпоинты (если нужны)
	// Route::get('/public-data', [...])->name('public.data');
});

// === 👨‍💼 АДМИН-ПАНЕЛЬ ===
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified']) // Требуется вход и подтвержденный email
	->group(function() {

		// Дашборд админа
		Route::get('/', function() {
			return view('admin.dashboard');
		})->name('dashboard');

		// === Управление чатами ===
		Route::prefix('chats')->name('chats.')->group(function() {

			// Список чатов (GET)
			Route::get('/', [ChatAdminController::class, 'index'])->name('index');

			// Форма создания чата (GET)
			Route::get('/create', [ChatAdminController::class, 'create'])->name('create');

			// Обработка создания чата (POST)
			Route::post('/', [ChatAdminController::class, 'store'])->name('store');

			// Просмотр чата (GET) — опционально
			Route::get('/{chat}', [ChatAdminController::class, 'show'])->name('show');

			// Форма редактирования (GET) — опционально
			Route::get('/{chat}/edit', [ChatAdminController::class, 'edit'])->name('edit');

			// Обновление чата (PUT/PATCH) — опционально
			Route::put('/{chat}', [ChatAdminController::class, 'update'])->name('update');

			// Удаление чата (DELETE) — опционально
			Route::delete('/{chat}', [ChatAdminController::class, 'destroy'])->name('destroy');

		});

		// === Другие разделы админки (заготовки) ===
		// Route::resource('users', AdminUserController::class);
		// Route::resource('settings', AdminSettingsController::class);
	});

// === 🚪 ВЫХОД ИЗ СИСТЕМЫ (Logout) ===
// Breeze может не создавать этот маршрут явно, добавляем для надежности
Route::post('/logout', function(Request $request) {
	Auth::logout();
	$request->session()->invalidate();
	$request->session()->regenerateToken();

	return redirect('/');
})->name('logout')->middleware('web');

// === 📄 ДОПОЛНИТЕЛЬНЫЕ СТРАНИЦЫ ===
Route::get('/about', function() {
	return view('about');
})->name('about');

Route::get('/profile', function() {
	return view('profile.edit');
})->middleware('auth')->name('profile.edit');