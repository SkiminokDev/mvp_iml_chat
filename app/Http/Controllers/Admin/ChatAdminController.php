<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatAdminController extends Controller
{
	/**
	 * Список всех чатов (для админа)
	 */
	public function index()
	{
		// Получаем пагинированный список чатов
		$chats = Chat::with('user')->latest()->paginate(20);
		//$chats = Chat::all();

		// Считаем статистику (отдельными запросами — это быстро с индексами)
		$stats = [
			'total' => $chats->total(), // Всего чатов (из всех страниц)
			'active' => Chat::where('is_active', true)->count(),
			'inactive' => Chat::where('is_active', false)->count(),
			//'active_on_page' => $chats->filter->is_active->count(), // Только на текущей странице
		];

		return view('admin.chats.index', compact('chats', 'stats'));
	}

	/**
	 * Форма создания нового чата
	 */
	public function create()
	{
		// Получаем список пользователей для выпадающего списка
		$users = User::select('id', 'email')
			->orderBy('email')
			->take(100)
			->get();

		return view('admin.chats.create', compact('users'));
	}

	/**
	 * Обработка создания чата
	 */
	public function store(Request $request)
	{
		// 1. Валидация данных
		$validated = $request->validate([
			'user_id' => 'required|exists:users,id',
			'title' => 'required|string|min:3|max:255',
			'is_active' => 'nullable|boolean',
		], [
			'user_id.required' => 'Выберите пользователя',
			'user_id.exists' => 'Пользователь не найден',
			'title.required' => 'Введите название чата',
			'title.min' => 'Название должно быть не менее 3 символов',
		]);

		try {
			// 2. Создание чата
			$chat = Chat::create([
				'user_id' => $validated['user_id'],
				'title' => $validated['title'],
				'is_active' => true, //$request->has('is_active'), // Чекбокс: есть = true, нет = false
				'description' => null,
				'is_archived' => false,
				'last_message_at' => null,
				'message_count' => 0,
			]);

			// 3. Логирование
			Log::info('Admin created chat', [
				'chat_id' => $chat->id,
				'user_id' => $chat->user_id,
				'admin_id' => auth()->id(),
			]);

			// 4. Редирект с сообщением
			return redirect()
				->route('admin.chats.index')
				->with('success', "Чат «{$chat->title}» успешно создан!");

		} catch (\Exception $e) {
			Log::error('Failed to create chat', ['error' => $e->getMessage()]);

			return back()
				->withInput()
				->with('error', 'Не удалось создать чат. Попробуйте позже.');
		}
	}
}
