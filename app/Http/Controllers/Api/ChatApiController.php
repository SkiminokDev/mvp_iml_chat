<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatApiController extends Controller
{
	/**
	 * GET /api/v1/chat/chats
	 * Список чатов пользователя (JSON)
	 */
	public function index(Request $request): JsonResponse
	{
		$user = $request->user();

		$chats = Chat::where('user_id', $user->id)
			->select('id', 'user_id', 'is_active', 'created_at', 'updated_at')
			->withCount('messages')
			->latest()
			->paginate($request->get('per_page', 20));

		return response()->json([
			'success' => true,
			'data' => $chats->items(),
			'pagination' => [
				'current_page' => $chats->currentPage(),
				'per_page' => $chats->perPage(),
				'total' => $chats->total(),
				'last_page' => $chats->lastPage(),
			],
		]);
	}

	/**
	 * GET /api/v1/chat/chats/{id}
	 * Детали чата (JSON)
	 */
	public function show(Request $request, int $chatId): JsonResponse
	{
		$user = $request->user();

		$chat = Chat::where('id', $chatId)
			->where('user_id', $user->id)
			->select('id', 'user_id', 'is_active', 'created_at', 'updated_at')
			->withCount('messages')
			->firstOrFail();

		return response()->json([
			'success' => true,
			'data' => $chat,
		]);
	}

	/**
	 * GET /api/v1/chat/chats/{chat}/messages
	 * История сообщений (JSON)
	 */
	public function getMessages(Request $request, int $chatId): JsonResponse
	{
		$user = $request->user();

		// Проверка прав
		$chat = Chat::where('id', $chatId)
			->where('user_id', $user->id)
			->firstOrFail();

		$messages = $chat->messages()
			->select('id', 'chat_id', 'text', 'sender', 'created_at')
			->orderBy('created_at', $request->get('order', 'asc'))
			->paginate($request->get('per_page', 50));

		return response()->json([
			'success' => true,
			'chat_id' => $chatId,
			'data' => $messages->items(),
			'pagination' => [
				'current_page' => $messages->currentPage(),
				'per_page' => $messages->perPage(),
				'total' => $messages->total(),
			],
		]);
	}
}