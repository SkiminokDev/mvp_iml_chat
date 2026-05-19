<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MessController extends Controller
{
	/**
	 * Обработка AJAX-запроса с сообщением
	 */
	public function send(Request $request): JsonResponse
	{
		// 1. Валидация данных
		$validated = $request->validate([
			'text' => 'required|string|min:1|max:1000',
		]);

		$messageText = trim($validated['text']);

		// 2. Обработка (логика бота)
		$botResponse = $this->processMessage($messageText);

		// 3. Сохранение в БД ⭐
		$message = Message::create([
			'text' => $messageText,
			'sender' => 'user',
			'response' => $botResponse,
		]);

		// 2. Логика обработки (пример)
		$message = trim($validated['text']);
		$responseText = $this->processMessage($message);

		// 3. Логирование (опционально)
		Log::info('Message received: ' . $message);

		// 4. Возвращаем JSON-ответ
		return response()->json([
			'success' => true,
			'message' => 'Сообщение принято',
			'data' => [
				'original' => $message,
				'response' => $responseText,
				'timestamp' => now()->format('H:i:s'),
			],
		], 200);
	}

	/**
	 * Пример бизнес-логики обработки сообщения
	 */
	private function processMessage(string $text): string
	{
		// Здесь может быть вызов вашего API, LLM, базы данных и т.д.

		// Простой пример: эхо + преобразование
		$text = mb_strtolower($text);

		if (str_contains($text, 'привет')) {
			return 'Привет! Чем могу помочь? 👋';
		}

		if (str_contains($text, 'как дела')) {
			return 'У меня всё отлично, спасибо! А у вас? 😊';
		}

		return 'Вы написали: "' . htmlspecialchars($text) . '". Я пока учусь отвечать! 🤖';
	}
}
