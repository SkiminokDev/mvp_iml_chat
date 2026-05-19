<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
	/**
	 * Список всех чатов (для пользователя)
	 */
	public function index()
	{
		$user = auth()->user();

		$chats = Chat::where('user_id', $user->id)
			->with(['messages' => function($q) {
				$q->latest()->limit(1);
			}])
			->latest()
			->paginate(20);

		$stats = [
			'total' => $chats->total(),
			'active' => Chat::where('user_id', $user->id)->where('is_active', true)->count(),
			'inactive' => Chat::where('user_id', $user->id)->where('is_active', false)->count(),
		];

		return view('user.chats.index', compact('chats', 'stats', 'user'));
	}

	/**
	 * Показать конкретный чат 🎯
	 * Маршрут: /user/chats/{chat}
	 */
	public function show($chatId)
	{
		$user = auth()->user();

		// 🔐 Проверяем, что чат принадлежит пользователю
		$chat = Chat::where('id', $chatId)
			->where('user_id', $user->id)
			->with(['messages' => function($query) {
				$query->orderBy('created_at', 'asc'); // Сообщения по порядку
			}])
			->firstOrFail();

		// 📋 Получаем список чатов для сайдбара (превью)
		$chatList = Chat::where('user_id', $user->id)
			->with(['messages' => function($q) {
				$q->latest()->limit(1);
			}])
			->latest()
			->get();

		return view('user.chats.show', compact('chat', 'chatList', 'user'));
	}

	/**
	 * API: Получить сообщения для чата (для AJAX) 🔄
	 * Маршрут: /api/user/chats/{chat}/messages
	 */
	public function getMessages($chatId)
	{
		$user = auth()->user();

		$chat = Chat::where('id', $chatId)
			->where('user_id', $user->id)
			->firstOrFail();

		$messages = $chat->messages()
			->orderBy('created_at', 'asc')
			->get()
			->map(function($msg) {
				return [
					'id' => $msg->id,
					'text' => $msg->text,
					'response' => $msg->response ?? null,
					'sender' => $msg->sender, // 'user' или 'bot'
					'created_at' => $msg->created_at?->format('H:i'),
				];
			});

		return response()->json([
			'success' => true,
			'chat_id' => $chat->id,
			'messages' => $messages,
		]);
	}
}
