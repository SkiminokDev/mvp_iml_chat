<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Класс для работы с внешними API мессенджеров
 * 
 * Получает настройки подключения из конфигурационного файла messengers.php
 * на основе ID клиента и названия мессенджера.
 */
class MessengerApiClient
{
    /**
     * Получить настройки подключения для клиента и мессенджера
     *
     * @param int $clientId ID клиента
     * @param string $messenger Название мессенджера
     * @return array|null Настройки подключения или null если не найдены
     */
    public function getClientSettings(int $clientId, string $messenger): ?array
    {
        $clients = config('messengers.clients');
        
        if (!isset($clients[$clientId][$messenger])) {
            return null;
        }
        
        $settings = $clients[$clientId][$messenger];
        $defaults = config('messengers.defaults');
        
        // Объединяем настройки с настройками по умолчанию
        return array_merge($defaults, $settings);
    }

    /**
     * Выполнить запрос к внешнему API мессенджера
     *
     * @param int $clientId ID клиента
     * @param string $messenger Название мессенджера
     * @param array $params Дополнительные параметры запроса
     * @return array Результат запроса
     * @throws Exception Если настройки не найдены или запрос не удался
     */
    public function request(int $clientId, string $messenger, array $params = []): array
    {
        $settings = $this->getClientSettings($clientId, $messenger);
        
        if (!$settings) {
            throw new Exception("Настройки для клиента {$clientId} и мессенджера {$messenger} не найдены");
        }
        
        $url = $settings['url'];
        $method = strtoupper($settings['method']);
        $token = $settings['token'] ?? null;
        $timeout = $settings['timeout'] ?? 30;
        $headers = $settings['headers'] ?? [];
        
        // Добавляем токен авторизации в заголовки
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }
        
        Log::info('Запрос к внешнему API мессенджера', [
            'client_id' => $clientId,
            'messenger' => $messenger,
            'url' => $url,
            'method' => $method,
        ]);
        
        try {
            $response = Http::withHeaders($headers)
                ->timeout($timeout)
                ->send($method, $url, [
                    'query' => $params,
                ]);
            
            $statusCode = $response->status();
            $body = $response->json() ?? $response->body();
            
            Log::info('Ответ от внешнего API мессенджера', [
                'client_id' => $clientId,
                'messenger' => $messenger,
                'status' => $statusCode,
            ]);
            
            if (!$response->successful()) {
                Log::error('Ошибка при запросе к внешнему API', [
                    'client_id' => $clientId,
                    'messenger' => $messenger,
                    'status' => $statusCode,
                    'response' => $body,
                ]);
                
                return [
                    'success' => false,
                    'status' => $statusCode,
                    'data' => $body,
                    'error' => "HTTP error {$statusCode}",
                ];
            }
            
            return [
                'success' => true,
                'status' => $statusCode,
                'data' => $body,
            ];
            
        } catch (Exception $e) {
            Log::error('Исключение при запросе к внешнему API', [
                'client_id' => $clientId,
                'messenger' => $messenger,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * GET запрос к API мессенджера
     *
     * @param int $clientId ID клиента
     * @param string $messenger Название мессенджера
     * @param array $params Параметры запроса
     * @return array Результат запроса
     */
    public function get(int $clientId, string $messenger, array $params = []): array
    {
        return $this->request($clientId, $messenger, $params);
    }

    /**
     * POST запрос к API мессенджера
     *
     * @param int $clientId ID клиента
     * @param string $messenger Название мессенджера
     * @param array $data Данные для отправки
     * @param array $params Дополнительные параметры запроса
     * @return array Результат запроса
     */
    public function post(int $clientId, string $messenger, array $data = [], array $params = []): array
    {
        $settings = $this->getClientSettings($clientId, $messenger);
        
        if (!$settings) {
            throw new Exception("Настройки для клиента {$clientId} и мессенджера {$messenger} не найдены");
        }
        
        $url = $settings['url'];
        $token = $settings['token'] ?? null;
        $timeout = $settings['timeout'] ?? 30;
        $headers = $settings['headers'] ?? [];
        
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }
        
        try {
            $response = Http::withHeaders($headers)
                ->timeout($timeout)
                ->post($url, $data);
            
            $statusCode = $response->status();
            $body = $response->json() ?? $response->body();
            
            if (!$response->successful()) {
                return [
                    'success' => false,
                    'status' => $statusCode,
                    'data' => $body,
                    'error' => "HTTP error {$statusCode}",
                ];
            }
            
            return [
                'success' => true,
                'status' => $statusCode,
                'data' => $body,
            ];
            
        } catch (Exception $e) {
            Log::error('Исключение при POST запросе к внешнему API', [
                'client_id' => $clientId,
                'messenger' => $messenger,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
}
