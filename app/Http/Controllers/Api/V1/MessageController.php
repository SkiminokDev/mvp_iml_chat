<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMessageRequest;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * Сохранение сообщения и создание/обновление разговора
     * 
     * POST /api/v1/messages
     */
    public function store(StoreMessageRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            DB::beginTransaction();
            
            // Проверяем существование продукта (ad_id)
            $product = Product::findOrFail($validated['ad_id']);
            
            // Формируем массив файлов (если есть)
            $attachments = !empty($validated['files']) ? $validated['files'] : null;
            
            // Ищем активный разговор для этой пары user_id + product_id
            $conversation = Conversation::where('user_id', $validated['user_id'])
                ->where('product_id', $validated['ad_id'])
                ->where('is_active', true)
                ->first();
            
            // Если разговор не найден - создаём новый
            if (!$conversation) {
                $conversation = Conversation::create([
                    'user_id' => $validated['user_id'],
                    'product_id' => $validated['ad_id'],
                    'title' => substr($validated['text'], 0, 50) . (strlen($validated['text']) > 50 ? '...' : ''),
                    'description' => null,
                    'is_active' => true,
                    'is_archived' => false,
                    'status' => 'active',
                    'last_message_at' => now(),
                    'message_count' => 0,
                    'metadata' => [
                        'created_via' => 'api',
                        'product_name' => $product->name ?? null,
                    ],
                    'settings' => [],
                ]);
                
                Log::info('Создан новый разговор', [
                    'conversation_id' => $conversation->id,
                    'user_id' => $validated['user_id'],
                    'product_id' => $validated['ad_id'],
                ]);
            }
            
            // Создаём сообщение
            $message = Message::create([
                'user_id' => $validated['user_id'],
                'conversation_id' => $conversation->id,
                'chat_id' => null,
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
            
            // Обновляем разговор: увеличиваем счётчик сообщений и обновляем время последнего сообщения
            $conversation->update([
                'last_message_at' => now(),
                'message_count' => $conversation->message_count + 1,
            ]);
            
            DB::commit();
            
            Log::info('Сообщение сохранено', [
                'message_id' => $message->id,
                'conversation_id' => $conversation->id,
                'user_id' => $validated['user_id'],
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
                    'conversation' => [
                        'id' => $conversation->id,
                        'title' => $conversation->title,
                        'message_count' => $conversation->message_count,
                        'last_message_at' => $conversation->last_message_at->toIso8601String(),
                    ],
                ],
            ], 201);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            
            Log::error('Продукт не найден', [
                'ad_id' => $validated['ad_id'] ?? null,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Продукт/объявление не найдено',
            ], 404);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Ошибка при сохранении сообщения', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $validated['user_id'] ?? null,
                'ad_id' => $validated['ad_id'] ?? null,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка сервера при сохранении сообщения',
            ], 500);
        }
    }
}
