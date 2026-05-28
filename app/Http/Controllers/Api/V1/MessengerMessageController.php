<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Api\MessengerApiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Контроллер для получения сообщений из внешних мессенджеров
 */
class MessengerMessageController extends Controller
{
    protected MessengerApiClient $client;

    public function __construct(MessengerApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Получить сообщения из внешнего мессенджера
     * 
     * GET /api/v1/messengers/messages?messenger={name_messenger}&client={id}
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $messenger = $request->query('messenger');
        $clientId = $request->query('client');

        // Валидация параметров
        if (!$messenger) {
            return response()->json([
                'success' => false,
                'message' => 'Параметр messenger обязателен',
            ], 400);
        }

        if (!$clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Параметр client обязателен',
            ], 400);
        }

        // Проверка что client_id является числом
        if (!is_numeric($clientId)) {
            return response()->json([
                'success' => false,
                'message' => 'Параметр client должен быть числом',
            ], 400);
        }

        $clientId = (int) $clientId;

        try {
            // Выполняем запрос к внешнему API через сервис
            $result = $this->client->get($clientId, $messenger);

            if ($result['success']) {
                Log::info('Успешное получение сообщений из мессенджера', [
                    'client_id' => $clientId,
                    'messenger' => $messenger,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Сообщения успешно получены',
                    'data' => $result['data'],
                ]);
            } else {
                Log::warning('Ошибка при получении сообщений из мессенджера', [
                    'client_id' => $clientId,
                    'messenger' => $messenger,
                    'error' => $result['error'] ?? 'Неизвестная ошибка',
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при получении сообщений',
                    'error' => $result['error'] ?? 'Неизвестная ошибка',
                    'data' => $result['data'] ?? null,
                ], $result['status'] ?? 500);
            }

        } catch (\Exception $e) {
            Log::error('Исключение при получении сообщений из мессенджера', [
                'client_id' => $clientId,
                'messenger' => $messenger,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
