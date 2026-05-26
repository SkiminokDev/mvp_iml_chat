<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMessageRequest;
use App\Models\Message;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * Сохранение сообщения и создание/обновление чата
     * 
     * POST /api/v1/messages
     */
    public function store(StoreMessageRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            DB::beginTransaction();
            
            // Формируем массив файлов (если есть)
            $attachments = !empty($validated['files']) ? $validated['files'] : null;
            
            // Ищем чат по chat_external_id
            $chat = Chat::where('external_id', $validated['chat_external_id'])->first();
            
            // Если чат не найден - создаём новый
            if (!$chat) {
                $chat = Chat::create([
                    'external_id' => $validated['chat_external_id'],
                    'user_id' => $validated['user_id'] ?? null,
                    'title' => substr($validated['text'], 0, 50) . (strlen($validated['text']) > 50 ? '...' : ''),
                    'description' => null,
                    'is_active' => true,
                    'is_archived' => false,
                    'last_message_at' => now(),
                    'message_count' => 0,
                ]);
                
                Log::info('Создан новый чат', [
                    'chat_id' => $chat->id,
                    'external_id' => $validated['chat_external_id'],
                    'user_id' => $validated['user_id'] ?? null,
                ]);
            }
            
            // Определяем user_id: если передан user_id используем его, иначе ищем по user_external_id
            $userId = $validated['user_id'] ?? null;
            if (!$userId && !empty($validated['user_external_id'])) {
                $user = User::where('external_id', $validated['user_external_id'])->first();
                if ($user) {
                    $userId = $user->id;
                }
            }
            
            // Создаём сообщение
            $message = Message::create([
                'user_id' => $userId,
                'user_external_id' => $validated['user_external_id'] ?? null,
                'chat_id' => $chat->id,
                'conversation_id' => null,
                'text' => $validated['text'],
                'sender' => 'user',
                'attachments' => $attachments,
                'metadata' => [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
                'ai_data' => null,
                'is_read' => false,
                'is_deleted' => false,
                'ip_address' => $request->ip(),
            ]);
            
            // Обновляем чат: увеличиваем счётчик сообщений и обновляем время последнего сообщения
            $chat->update([
                'last_message_at' => now(),
                'message_count' => $chat->message_count + 1,
            ]);
            
            DB::commit();
            
            Log::info('Сообщение сохранено', [
                'message_id' => $message->id,
                'chat_id' => $chat->id,
                'user_id' => $userId,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Сообщение успешно сохранено',
                'data' => [
                    'message' => [
                        'id' => $message->id,
                        'text' => $message->text,
                        'sender' => $message->sender,
                        'attachments' => $message->attachments,
                        'created_at' => $message->created_at->toIso8601String(),
                    ],
                    'chat' => [
                        'id' => $chat->id,
                        'external_id' => $chat->external_id,
                        'title' => $chat->title,
                        'message_count' => $chat->message_count,
                        'last_message_at' => $chat->last_message_at->toIso8601String(),
                    ],
                ],
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Ошибка при сохранении сообщения', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'chat_external_id' => $validated['chat_external_id'] ?? null,
                'user_external_id' => $validated['user_external_id'] ?? null,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка сервера при сохранении сообщения',
            ], 500);
        }
    }
}
