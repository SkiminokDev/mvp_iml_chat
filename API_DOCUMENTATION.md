# API Документация - Мессенджеры

## Обзор

Данный документ описывает новую функциональность для работы с внешними мессенджерами через единый API интерфейс. Реализована система получения сообщений из различных внешних сервисов (мессенджеров) с гибкой конфигурацией по клиентам.

---

## Архитектура

### Компоненты системы

1. **Конфигурационный файл** (`config/messengers.php`) - хранит настройки подключения к внешним API
2. **Сервисный класс** (`MessengerApiClient`) - клиент для выполнения запросов к внешним API
3. **Контроллер** (`MessengerMessageController`) - обработка входящих HTTP запросов
4. **Console Command** (`GetMessengerMessagesCommand`) - фоновая задача для cron
5. **Планировщик** (`routes/console.php`) - расписание выполнения задач

---

## Конфигурация

### Файл: `config/messengers.php`

Конфигурационный файл содержит настройки для всех клиентов и мессенджеров. Для добавления нового мессенджера достаточно добавить новую запись в массив `clients`.

#### Структура конфигурации:

```php
'clients' => [
    // ID клиента
    1 => [
        // Название мессенджера
        'custom' => [
            'url' => 'https://mes.contakt-servis.ru/api/v1/messeges',
            'method' => 'GET',
            'token' => '123456789',
            'timeout' => 30,
            'headers' => [
                'Custom-Header' => 'value'
            ],
        ],
    ],
],
```

#### Доступные параметры:

| Параметр | Тип | Обязательный | Описание |
|----------|-----|--------------|----------|
| `url` | string | Да | URL внешнего API |
| `method` | string | Нет | HTTP метод (GET, POST, PUT, DELETE). По умолчанию: GET |
| `token` | string | Да | Токен авторизации |
| `timeout` | int | Нет | Таймаут запроса в секундах. По умолчанию: 30 |
| `headers` | array | Нет | Дополнительные HTTP заголовки |

#### Пример добавления нового мессенджера:

```php
// Добавляем Telegram для клиента 2
2 => [
    'telegram' => [
        'url' => 'https://api.telegram.org/bot<TOKEN>/getUpdates',
        'method' => 'GET',
        'token' => 'your_telegram_bot_token',
        'timeout' => 60,
    ],
],

// Добавляем WhatsApp для клиента 3
3 => [
    'whatsapp' => [
        'url' => 'https://api.whatsapp.com/messages',
        'method' => 'POST',
        'token' => 'your_whatsapp_token',
        'headers' => [
            'Content-Type' => 'application/json',
        ],
    ],
],
```

---

## API Endpoints

### GET /api/v1/messengers/messages

Получение сообщений из внешнего мессенджера.

**URL:** `GET /api/v1/messengers/messages`

**Параметры запроса (query parameters):**

| Параметр | Тип | Обязательный | Описание |
|----------|-----|--------------|----------|
| `messenger` | string | Да | Название мессенджера (например: `custom`, `telegram`, `whatsapp`) |
| `client` | int | Да | ID клиента |

**Пример запроса:**

```bash
curl -X GET "http://localhost/api/v1/messengers/messages?messenger=custom&client=1" \
  -H "Accept: application/json"
```

**Ответ при успехе (200 OK):**

```json
{
  "success": true,
  "message": "Сообщения успешно получены",
  "data": {
    // Данные от внешнего API
  }
}
```

**Ответ при ошибке:**

```json
{
  "success": false,
  "message": "Ошибка при получении сообщений",
  "error": "HTTP error 404",
  "data": null
}
```

**Коды ответов:**

| Код | Описание |
|-----|----------|
| 200 | Успешное получение сообщений |
| 400 | Неверные параметры запроса (отсутствует messenger или client) |
| 500 | Внутренняя ошибка сервера или ошибка внешнего API |

---

## Console Command (Cron)

### Команда: `messengers:get-messages`

Команда для автоматического получения сообщений из внешних мессенджеров по расписанию.

**Сигнатура:**

```bash
php artisan messengers:get-messages 
    {--messenger=custom : Название мессенджера}
    {--client=1 : ID клиента}
```

**Примеры использования:**

```bash
# Получить сообщения для клиента 1 из мессенджера custom
php artisan messengers:get-messages --messenger=custom --client=1

# Получить сообщения для клиента 2 из telegram
php artisan messengers:get-messages --messenger=telegram --client=2
```

### Настройка Cron

Команда настроена на выполнение **каждую минуту** через планировщик Laravel.

#### 1. Добавьте в crontab сервера:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 2. Расписание настроено в файле `routes/console.php`:

```php
Schedule::command('messengers:get-messages --messenger=custom --client=1')
    ->everyMinute()
    ->name('get-messenger-messages')
    ->withoutOverlapping();
```

#### 3. Логирование

Результат выполнения команды записывается в файл:

```
storage/logs/cron_get_messages.txt
```

**Формат лога:**

```
========================================
[2024-01-15T10:30:00+00:00] Запуск получения сообщений
  Мессенджер: custom
  Клиент ID: 1
  Статус: УСПЕШНО
  HTTP статус: 200
  Получено данных: 5 элементов
  Сообщений: 3
----------------------------------------
```

---

## Сервисный класс: MessengerApiClient

**Путь:** `app/Services/Api/MessengerApiClient.php`

Класс предоставляет методы для работы с внешними API мессенджеров.

### Публичные методы:

#### `getClientSettings(int $clientId, string $messenger): ?array`

Получение настроек подключения для указанного клиента и мессенджера.

```php
$settings = $client->getClientSettings(1, 'custom');
// Возвращает массив настроек или null если не найдено
```

#### `request(int $clientId, string $messenger, array $params = []): array`

Выполнение запроса к внешнему API.

```php
$result = $client->request(1, 'custom', ['param1' => 'value1']);
// Возвращает: ['success' => bool, 'status' => int, 'data' => mixed, 'error' => string|null]
```

#### `get(int $clientId, string $messenger, array $params = []): array`

GET запрос к API мессенджера.

```php
$result = $client->get(1, 'custom', ['limit' => 10]);
```

#### `post(int $clientId, string $messenger, array $data = [], array $params = []): array`

POST запрос к API мессенджера.

```php
$result = $client->post(1, 'custom', ['message' => 'Hello']);
```

---

## Контроллер: MessengerMessageController

**Путь:** `app/Http/Controllers/Api/V1/MessengerMessageController.php`

Обрабатывает HTTP запросы к эндпоинту `/api/v1/messengers/messages`.

### Методы:

#### `index(Request $request): JsonResponse`

Обработка GET запроса на получение сообщений.

- Выполняет валидацию параметров
- Вызывает сервисный класс для запроса к внешнему API
- Возвращает JSON ответ с результатом

---

## Маршруты (Routes)

**Файл:** `routes/api.php`

```php
Route::middleware(config('api.auth_required') ? ['auth:sanctum'] : [])
    ->prefix('v1')
    ->group(function () {
        // Получение сообщений из внешних мессенджеров
        Route::get('/messengers/messages', 
            [\App\Http\Controllers\Api\V1\MessengerMessageController::class, 'index'])
            ->name('messengers.messages');
    });
```

---

## Быстрый старт

### 1. Добавить конфигурацию нового клиента

Откройте `config/messengers.php` и добавьте:

```php
'clients' => [
    1 => [
        'custom' => [
            'url' => 'https://mes.contakt-servis.ru/api/v1/messeges',
            'method' => 'GET',
            'token' => '123456789',
            'timeout' => 30,
        ],
    ],
    // Добавьте свои настройки здесь
],
```

### 2. Проверить работу API

```bash
curl -X GET "http://localhost/api/v1/messengers/messages?messenger=custom&client=1"
```

### 3. Проверить работу cron команды

```bash
php artisan messengers:get-messages --messenger=custom --client=1
```

### 4. Проверить логи

```bash
cat storage/logs/cron_get_messages.txt
```

### 5. Настроить системный cron

```bash
crontab -e
# Добавить строку:
* * * * * cd /workspace && php artisan schedule:run >> /dev/null 2>&1
```

---

## Расширение функциональности

### Добавление нового мессенджера

1. Откройте `config/messengers.php`
2. Добавьте новую запись в массив `clients`:

```php
4 => [
    'viber' => [
        'url' => 'https://chatapi.viber.com/pa/get_chat_history',
        'method' => 'POST',
        'token' => 'your_viber_token',
        'headers' => [
            'X-Viber-Auth-Token' => 'your_viber_token',
        ],
    ],
],
```

3. Готово! Новый мессенджер доступен через API:

```bash
curl "http://localhost/api/v1/messengers/messages?messenger=viber&client=4"
```

### Добавление новой cron задачи для другого клиента

Откройте `routes/console.php` и добавьте:

```php
Schedule::command('messengers:get-messages --messenger=telegram --client=2')
    ->everyFiveMinutes()
    ->name('get-telegram-messages-client-2');
```

---

## Безопасность

- Токены авторизации хранятся в конфигурационном файле
- Рекомендуется использовать environment variables для чувствительных данных
- API endpoint может быть защищен middleware `auth:sanctum` (настраивается в `config/api.php`)

---

## Логирование

Все запросы и ответы логируются через Laravel Log:

- Запросы к внешним API - уровень `info`
- Ошибки запросов - уровень `error`
- Исключения - уровень `error` с трассировкой

Результаты cron задачи дублируются в текстовый файл `storage/logs/cron_get_messages.txt`

---

## Версия документации

**Версия:** 1.0  
**Дата обновления:** 2024  
**Автор:** llmBot
