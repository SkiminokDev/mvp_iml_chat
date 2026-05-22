# Тестирование API сообщений (Postman)

Этот файл содержит набор запросов для тестирования эндпоинта `POST /api/v1/messages` с помощью Postman.

## Предварительные требования

1.  Запустите локальный сервер:
    ```bash
    php artisan serve
    ```
    Сервер должен быть доступен по адресу: `http://127.0.0.1:8000`

2.  Убедитесь, что база данных настроена и миграции выполнены:
    ```bash
    php artisan migrate --seed
    ```
    *Сидеры создадут тестовых пользователей и продукты.*

3.  Откройте Postman.

---

## Сценарий 1: Подготовка данных (Получение ID продукта)

Перед отправкой сообщения необходимо убедиться, что у нас есть валидный `ad_id` (ID продукта).

**Запрос:** `GET Product List`
*   **Method:** `GET`
*   **URL:** `http://127.0.0.1:8000/api/v1/products` (или используйте прямой запрос к БД/сидерам, если эндпоинт продуктов еще не создан)
*   **Альтернатива (если нет эндпоинта продуктов):**
    Используйте известные ID из сидеров. Обычно первый продукт имеет ID `1`.
    В дальнейших запросах мы будем использовать `ad_id: 1`.

---

## Сценарий 2: Успешное создание сообщения (Новая беседа)

Этот запрос создаст новую беседу (`conversation`) и первое сообщение в ней.

**Запрос:** `Create Message (New Conversation)`
*   **Method:** `POST`
*   **URL:** `http://127.0.0.1:8000/api/v1/messages`
*   **Headers:**
    *   `Content-Type`: `multipart/form-data` (Postman установит автоматически при выборе Body)
    *   `Accept`: `application/json`
*   **Body (form-data):**

| Key | Value | Type |
| :--- | :--- | :--- |
| `user_id` | `1` | Text |
| `ad_id` | `1` | Text |
| `text` | `Здравствуйте, интересен ли еще этот товар?` | Text |
| `files` | *(Выберите любой тестовый файл)* | File |

*Примечание: Убедитесь, что пользователь с ID `1` существует в БД.*

**Ожидаемый ответ:**
*   **Status:** `200 OK` или `201 Created`
*   **Body:** JSON объект с данными сообщения и информацией о созданной беседе.
    ```json
    {
      "success": true,
      "data": {
        "message": { ... },
        "conversation": { ... }
      }
    }
    ```

---

## Сценарий 3: Добавление сообщения в существующую беседу

Отправьте повторный запрос с теми же `user_id` и `ad_id`. Система должна найти существующую активную беседу и добавить сообщение туда, а не создавать новую.

**Запрос:** `Create Message (Existing Conversation)`
*   **Method:** `POST`
*   **URL:** `http://127.0.0.1:8000/api/v1/messages`
*   **Body (form-data):**

| Key | Value | Type |
| :--- | :--- | :--- |
| `user_id` | `1` | Text |
| `ad_id` | `1` | Text |
| `text` | `Какая цена будет окончательной?` | Text |

**Ожидаемый ответ:**
*   **Status:** `200 OK`
*   **Body:** JSON объект, где `conversation.id` совпадает с ID из Сценария 2, но счетчик `messages_count` увеличен.

---

## Сценарий 4: Негативные тесты (Валидация)

Проверка работы валидатора FormRequest.

### 4.1. Отсутствие обязательных полей
**Запрос:** `Validation Error - Missing Fields`
*   **Method:** `POST`
*   **URL:** `http://127.0.0.1:8000/api/v1/messages`
*   **Body (form-data):**
    *   `user_id`: `1`
    *   *(поля `ad_id` и `text` отсутствуют)*

**Ожидаемый ответ:**
*   **Status:** `422 Unprocessable Entity`
*   **Body:** Сообщение об ошибке валидации.
    ```json
    {
      "message": "The ad id field is required.",
      "errors": { ... }
    }
    ```

### 4.2. Несуществующий пользователь
**Запрос:** `Validation Error - Invalid User`
*   **Method:** `POST`
*   **URL:** `http://127.0.0.1:8000/api/v1/messages`
*   **Body (form-data):**
    *   `user_id`: `99999`
    *   `ad_id`: `1`
    *   `text`: `Тест`

**Ожидаемый ответ:**
*   **Status:** `422 Unprocessable Entity`
*   **Ошибка:** `The selected user id is invalid.`

### 4.3. Несуществующий товар
**Запрос:** `Validation Error - Invalid Ad`
*   **Method:** `POST`
*   **URL:** `http://127.0.0.1:8000/api/v1/messages`
*   **Body (form-data):**
    *   `user_id`: `1`
    *   `ad_id`: `99999`
    *   `text`: `Тест`

**Ожидаемый ответ:**
*   **Status:** `422 Unprocessable Entity`
*   **Ошибка:** `The selected ad id is invalid.`

---

## Коллекция для импорта (Raw JSON)

Вы можете скопировать этот JSON, сохранить его как файл `.json` и импортировать в Postman через кнопку **Import**.

```json
{
  "info": {
    "name": "Messages API Tests",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Create Message (New Conversation)",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "formdata",
          "formdata": [
            {
              "key": "user_id",
              "value": "1",
              "type": "text"
            },
            {
              "key": "ad_id",
              "value": "1",
              "type": "text"
            },
            {
              "key": "text",
              "value": "Здравствуйте, интересен ли еще этот товар?",
              "type": "text"
            },
            {
              "key": "files",
              "type": "file",
              "src": []
            }
          ]
        },
        "url": {
          "raw": "http://127.0.0.1:8000/api/v1/messages",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "v1", "messages"]
        }
      }
    },
    {
      "name": "Validation Error - Missing Fields",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "formdata",
          "formdata": [
            {
              "key": "user_id",
              "value": "1",
              "type": "text"
            }
          ]
        },
        "url": {
          "raw": "http://127.0.0.1:8000/api/v1/messages",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "v1", "messages"]
        }
      }
    },
    {
      "name": "Validation Error - Invalid User",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "formdata",
          "formdata": [
            {
              "key": "user_id",
              "value": "99999",
              "type": "text"
            },
            {
              "key": "ad_id",
              "value": "1",
              "type": "text"
            },
            {
              "key": "text",
              "value": "Test",
              "type": "text"
            }
          ]
        },
        "url": {
          "raw": "http://127.0.0.1:8000/api/v1/messages",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "v1", "messages"]
        }
      }
    }
  ]
}
```

## Автоматизация (Optional)

Для автоматического запуска тестов в терминале можно использовать коллекцию Postman вместе с Newman:

1.  Установите Newman:
    ```bash
    npm install -g newman
    ```
2.  Экспортируйте коллекцию из Postman в файл `messages_collection.json`.
3.  Запустите тесты:
    ```bash
    newman run messages_collection.json
    ```
