# Архитектура системы сообщений и товаров

## Обзор
Система предназначена для управления диалогами между пользователями и товарами/объявлениями, с логированием действий AI и возможностью прикрепления файлов.

---

## Таблицы базы данных

### 1. `products` (Товары/Объявления)
Хранит информацию о товарах или объявлениях, к которым могут быть привязаны сообщения.
- `id`: Primary Key
- `title`: Название товара
- `description`: Описание
- `price`: Цена
- `metadata`: JSON (дополнительные характеристики, атрибуты)
- `timestamps`: created_at, updated_at

### 2. `conversations` (Разговоры)
Агрегирует сообщения между пользователем и товаром.
- `id`: Primary Key
- `user_id`: Foreign Key -> `users.id` (Инициатор разговора)
- `product_id`: Foreign Key -> `products.id` (Товар, обсуждаемый в разговоре)
- `status`: Статус разговора (active, closed, archived)
- `message_count`: Счётчик сообщений (денормализация для производительности)
- `last_message_at`: Время последнего сообщения
- `metadata`: JSON (дополнительные данные, например, теги, настройки)
- `timestamps`: created_at, updated_at

### 3. `messages` (Сообщения)
Отдельные сообщения в рамках разговора.
- `id`: Primary Key
- `conversation_id`: Foreign Key -> `conversations.id`
- `user_id`: Foreign Key -> `users.id` (Автор сообщения)
- `text`: Текст сообщения
- `attachments`: JSON (массив файлов: пути, имена, типы MIME)
- `metadata`: JSON (дополнительные данные: источник, флаги)
- `ip_address`: IP адрес отправителя
- `timestamps`: created_at, updated_at

### 4. `ai_logs` (Логи AI)
Журнал действий и ответов искусственного интеллекта.
- `id`: Primary Key
- `conversation_id`: Foreign Key -> `conversations.id` (Опционально, если лог привязан к разговору)
- `message_id`: Foreign Key -> `messages.id` (Опционально, если лог привязан к сообщению)
- `action`: Действие AI (summarize, suggest_reply, moderate, etc.)
- `input_data`: JSON (входные данные для AI)
- `output_data`: JSON (результат работы AI)
- `tokens_used`: Количество использованных токенов
- `model`: Название модели AI
- `timestamps`: created_at, updated_at

### 5. `clients` (Клиенты)
Хранит информацию о клиентах системы.
- `id`: Primary Key
- `name`: Имя клиента
- `active`: Статус активности (boolean)
- `active_data`: Дата и время активации
- `balance`: Баланс клиента (integer)
- `timestamps`: created_at, updated_at

### 6. `chats` (Чаты)
Диалоги между клиентами и пользователями.
- `id`: Primary Key
- `user_id`: Foreign Key -> `users.id`
- `client_id`: Foreign Key -> `clients.id` (Связь с клиентом)
- `title`: Заголовок чата
- `description`: Описание
- `is_active`: Статус активности чата
- `is_archived`: Статус архивации
- `last_message_at`: Время последнего сообщения
- `message_count`: Счётчик сообщений
- `timestamps`: created_at, updated_at

---

## Взаимосвязи таблиц (ER-Diagram логика)

```text
users (1) ----< (N) conversations
products (1) --< (N) conversations
conversations (1) ----< (N) messages
users (1) ----< (N) messages
conversations (1) ----< (N) ai_logs
messages (1) ------< (N) ai_logs
clients (1) ----< (N) chats
users (1) ----< (N) chats
```

**Детали связей:**
1. **User <-> Conversations**: Один пользователь может иметь много разговоров (как инициатор).
2. **Product <-> Conversations**: Один товар может быть предметом многих разговоров.
3. **Conversation <-> Messages**: Один разговор содержит много сообщений.
4. **User <-> Messages**: Один пользователь может написать много сообщений.
5. **Conversation <-> AiLogs**: К одному разговору может относиться много логов AI.
6. **Message <-> AiLogs**: К одному сообщению может относиться много логов AI (например, модерация + генерация ответа).
7. **Client <-> Chats**: Один клиент может иметь много чатов.
8. **User <-> Chats**: Один пользователь может участвовать во многих чатах.

---

## Eloquent Модели и Отношения

### `App\Models\Product`
- **Таблица**: `products`
- **Касты**:
  - `metadata`: `array` (автоматическая сериализация JSON)
- **Отношения**:
  - `conversations()`: `hasMany(Conversation::class)` — Получение всех разговоров по этому товару.

### `App\Models\Conversation`
- **Таблица**: `conversations`
- **Касты**:
  - `metadata`: `array`
- **Отношения**:
  - `user()`: `belongsTo(User::class)` — Инициатор разговора.
  - `product()`: `belongsTo(Product::class)` — Товар разговора.
  - `messages()`: `hasMany(Message::class)` — Список сообщений.
  - `aiLogs()`: `hasMany(AiLog::class)` — Логи AI, связанные с разговором.

### `App\Models\Message`
- **Таблица**: `messages`
- **Касты**:
  - `attachments`: `array` (список файлов)
  - `metadata`: `array`
- **Отношения**:
  - `conversation()`: `belongsTo(Conversation::class)` — Родительский разговор.
  - `user()`: `belongsTo(User::class)` — Автор сообщения.
  - `aiLogs()`: `hasMany(AiLog::class)` — Логи AI, обработанные для этого сообщения.

### `App\Models\AiLog`
- **Таблица**: `ai_logs`
- **Касты**:
  - `input_data`: `array`
  - `output_data`: `array`
- **Отношения**:
  - `conversation()`: `belongsTo(Conversation::class)` — Связанный разговор (если есть).
  - `message()`: `belongsTo(Message::class)` — Связанное сообщение (если есть).

### `App\Models\Client`
- **Таблица**: `clients`
- **Касты**:
  - `active`: `boolean`
  - `active_data`: `datetime`
  - `balance`: `integer`
- **Отношения**:
  - `chats()`: `hasMany(Chat::class)` — Список чатов клиента.

### `App\Models\Chat`
- **Таблица**: `chats`
- **Касты**:
  - `is_active`: `boolean`
  - `is_archived`: `boolean`
  - `last_message_at`: `datetime`
- **Отношения**:
  - `user()`: `belongsTo(User::class)` — Пользователь чата.
  - `client()`: `belongsTo(Client::class)` — Клиент чата.
  - `messages()`: `hasMany(Message::class)` — Список сообщений.
  - `lastMessage()`: `hasOne(Message::class)->latestOfMany()` — Последнее сообщение.

---

## Контроллеры и Запросы

### `App\Http\Controllers\Admin\ClientController`
Контроллер для управления клиентами в административной панели.
- **Метод `index()`**:
  - Отображает список всех клиентов с пагинацией.
  - Поддерживает фильтры: `filter_id`, `filter_name`, `filter_active`.
  - Возвращает представление `admin.clients.index`.
  
- **Метод `show($clientId)`**:
  - Отображает детальную информацию о клиенте.
  - Показывает список чатов клиента с пагинацией.
  - Возвращает представление `admin.clients.show`.

### `App\Http\Requests\Api\StoreMessageRequest`
Класс форм-запроса для валидации входящих данных при создании сообщения.
- **Правила валидации**:
  - `user_id`: required, integer, exists:users,id
  - `ad_id` (product_id): required, integer, exists:products,id
  - `text`: required, string, max:5000
  - `files`: optional, array, max:10 элементов
  - `files.*`: file, max:5MB, mime:image/jpeg,image/png,application/pdf

### `App\Http\Controllers\Api\V1\MessageController`
Обрабатывает бизнес-логику создания сообщения.
- **Метод `store()`**:
  1. Валидирует данные через `StoreMessageRequest`.
  2. Запускает транзакцию БД.
  3. Ищет активный разговор (`status='active'`) между `user_id` и `product_id`.
  4. Если разговор не найден — создает новый.
  5. Создает запись в таблице `messages` (с обработкой файлов в `attachments`).
  6. Обновляет счетчик `message_count` и `last_message_at` в таблице `conversations`.
  7. Возвращает JSON ответ с данными сообщения и разговора.

---

## Фабрики и Сидеры

### Фабрики (Factories)
Генерируют фейковые данные для тестирования.
- `ProductFactory`: Генерирует title, price, random metadata.
- `ConversationFactory`: Генерирует статус, случайные user/product, metadata.
- `MessageFactory`: Генерирует текст, массив attachments, ip_address.
- `AiLogFactory`: Генерирует action, input/output JSON, tokens.
- `ClientFactory`: Генерирует name, active, active_data, balance.
- `ChatFactory`: Генерирует title, description, is_active, is_archived, client_id, user_id.

### Сидеры (Seeders)
- `DatabaseSeeder`: Вызывает остальные сидеры.
- `ProductSeeder`: Создает набор тестовых товаров.
- `ConversationSeeder`: Создает тестовые диалоги, используя фабрики.
- `MessageSeeder`: Наполняет диалоги сообщениями.
- `AiLogSeeder`: Добавляет логи AI для анализа.
- `ClientSeeder`: Создает тестовых клиентов.
- `ChatSeeder`: Наполняет чаты данными.

---

## Маршруты (Routes)

### API Routes (`routes/api.php`)

| Метод | URI | Действие | Описание |
|-------|-----|----------|----------|
| POST | `/api/v1/messages` | `MessageController@store` | Создание нового сообщения и управление разговором |

### Admin Routes (`routes/admin.php`)

| Метод | URI | Действие | Описание |
|-------|-----|----------|----------|
| GET | `/admin/clients` | `ClientController@index` | Список клиентов с фильтрами и пагинацией |
| GET | `/admin/clients/{clientId}` | `ClientController@show` | Детальная информация о клиенте и его чатах |
| GET | `/admin/chats` | `ChatAdminController@index` | Список всех чатов |
| GET | `/admin/authors` | `AuthorController@index` | Список авторов |

---

## Поток данных (Data Flow) при создании сообщения

1. **Client** отправляет POST запрос с `user_id`, `ad_id`, `text`, `files`.
2. **Router** направляет запрос в `MessageController`.
3. **Validator** (`StoreMessageRequest`) проверяет данные.
4. **Controller**:
   - Начинает DB Transaction.
   - Проверяет таблицу `conversations` на наличие активного диалога.
   - Если нет -> `INSERT INTO conversations`.
   - Обрабатывает файлы -> сохраняет пути в массив.
   - `INSERT INTO messages` (с JSON в `attachments`).
   - `UPDATE conversations` (инкремент счетчика, обновление времени).
   - Коммитит транзакцию.
5. **Response**: JSON объект с созданной сущностью.
