<?php

namespace App\Console\Commands;

use App\Services\Api\MessengerApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Команда для получения сообщений из внешних мессенджеров через cron
 * 
 * Запускается раз в минуту через планировщик задач Laravel.
 * Результат выполнения логируется в файл storage/logs/cron_get_messages.txt
 */
class GetMessengerMessagesCommand extends Command
{
    /**
     * Имя и сигнатура команды консольной команды.
     *
     * @var string
     */
    protected $signature = 'messengers:get-messages 
                            {--messenger=custom : Название мессенджера}
                            {--client=1 : ID клиента}';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Получение сообщений из внешних мессенджеров по расписанию (cron)';

    protected MessengerApiClient $client;

    /**
     * Путь к файлу логирования
     */
    protected string $logFile = 'storage/logs/cron_get_messages.txt';

    public function __construct(MessengerApiClient $client)
    {
        parent::__construct();
        $this->client = $client;
    }

	/**
	 * Обьединение текстовых сообщений
	*/
	private function getAllMess(array $params): string
	{
		$text = '';
		if (count($params) > 1) {
			foreach($params as $item){
				$text .= $item['type_user'] .': ' . $item['text'] . '| ';
			}
		} else {
			$text = $item['type_user'] . ': ' . $params[0]['text'] . '| ';
		}
		return $text;
	}

    /**
     * Выполнение команды.
     */
    public function handle(): int
    {
        $messenger = $this->option('messenger');
        $clientId = (int) $this->option('client');
        
        $timestamp = now()->toIso8601String();
        
        // Формируем сообщение для логирования
        $logMessage = "========================================\n";
        $logMessage .= "[{$timestamp}] Запуск получения сообщений\n";
        $logMessage .= "  Мессенджер: {$messenger}\n";
        $logMessage .= "  Клиент ID: {$clientId}\n";
        
        try {
            // Выполняем запрос к внешнему API
            $result = $this->client->get($clientId, $messenger);
            
            if ($result['success']) {
                $logMessage .= "  Статус: УСПЕШНО\n";
                $logMessage .= "  HTTP статус: {$result['status']}\n";
                
                // Логируем данные ответа (кратко)
                if (is_array($result['data'])) {
                    $dataCount = count($result['data']);
                    $logMessage .= "  Получено данных: {$dataCount} элементов\n";
                    
                    // Если есть сообщения, можно добавить краткую информацию
                    if (isset($result['data']['messages']) && is_array($result['data']['messages'])) {
                        //$messageCount = count($result['data']['messages']);
                        //$logMessage .= "  Сообщений: {$messageCount}\n";
                    }
                } else {
                    $logMessage .= "  Ответ: " . substr((string)$result['data'], 0, 200) . "\n";
                }
	            print_r($result);
				$this->info("Сообщения успешно получены");
				// запуск логики обработки сообщений
	            // выбираем все неотвеченные сообщения объединяем их в один текст
	            $imMessage = $this->getAllMess($result['data']['data']);

				// Делаем запрос в нейронку сделать анализ текста, что хочет клиент
	            // запрос услуг, запрос цены, запрос параметров товара ..


                
            } else {
                $logMessage .= "  Статус: ОШИБКА\n";
                $logMessage .= "  HTTP статус: {$result['status']}\n";
                $logMessage .= "  Ошибка: " . ($result['error'] ?? 'Неизвестная ошибка') . "\n";
                
                $this->warn("Ошибка при получении сообщений: " . ($result['error'] ?? 'Неизвестная ошибка'));
            }
            
        } catch (\Exception $e) {
            $logMessage .= "  Статус: ИСКЛЮЧЕНИЕ\n";
            $logMessage .= "  Ошибка: " . $e->getMessage() . "\n";
            $logMessage .= "  Трассировка: " . $e->getTraceAsString() . "\n";
            
            Log::error('Cron GetMessengerMessages - исключение', [
                'messenger' => $messenger,
                'client_id' => $clientId,
                'error' => $e->getMessage(),
            ]);
            
            $this->error("Исключение: " . $e->getMessage());
        }
        
        $logMessage .= "----------------------------------------\n\n";
        
        // Записываем лог в файл
        $fullPath = base_path($this->logFile);
        file_put_contents($fullPath, $logMessage, FILE_APPEND | LOCK_EX);
        
        Log::info('Cron GetMessengerMessages выполнен', [
            'messenger' => $messenger,
            'client_id' => $clientId,
            'log_file' => $fullPath,
        ]);
        
        return Command::SUCCESS;
    }
}
