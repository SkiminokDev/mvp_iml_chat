@extends('layouts.app')

@section('title', 'Чат с ботом')

@section('content')
    <div class="chat-container" style="max-width: 600px; margin: 0 auto; padding: 20px;">

        <h1>💬 Чат с ассистентом</h1>

        {{-- Поле для вывода ответов --}}
        <div id="chatBox" style="
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        min-height: 200px;
        max-height: 400px;
        overflow-y: auto;
        background: #f9f9f9;
        margin-bottom: 15px;
    ">
            <div class="message bot" style="margin-bottom: 10px;">
                <strong>Бот:</strong>
                <span>Привет! Напишите сообщение, и я отвечу.</span>
            </div>
        </div>

        {{-- Форма отправки --}}
        <form id="messageForm" style="display: flex; gap: 10px;">
            <input
                    type="text"
                    id="messageInput"
                    name="text"
                    placeholder="Введите сообщение..."
                    style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;"
                    autocomplete="off"
            >
            <button
                    type="submit"
                    id="sendBtn"
                    style="
                padding: 10px 20px;
                background: #007bff;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            "
            >
                Отправить
            </button>
        </form>

        {{-- Блок для ошибок --}}
        <div id="errorMsg" style="color: #dc3545; margin-top: 10px; display: none;"></div>

    </div>

    {{-- CSRF Token (обязательно!) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('messageForm');
        const input = document.getElementById('messageInput');
        const chatBox = document.getElementById('chatBox');
        const sendBtn = document.getElementById('sendBtn');
        const errorMsg = document.getElementById('errorMsg');

        form.addEventListener('submit', async function(e) {
          e.preventDefault(); // Предотвращаем перезагрузку страницы

          const message = input.value.trim();
          if (!message) return;

          // UI: блокируем кнопку и показываем статус
          setLoading(true);
          hideError();

          // Добавляем сообщение пользователя в чат
          addMessage('Вы', message, 'user');
          input.value = '';

          try {
            const response = await fetch('/api/message', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              },
              body: JSON.stringify({ text: message })
            });

            // Парсим ответ
            const result = await response.json();

            if (response.ok && result.success) {
              // Успех: показываем ответ бота
              addMessage('Бот', result.data.response, 'bot');
            } else {
              // Ошибка логики приложения
              showError(result.message || 'Произошла ошибка');
            }

          } catch (error) {
            console.error('AJAX Error:', error);
            showError('Не удалось соединиться с сервером');

            // Откат: возвращаем текст в поле, если ошибка сети
            input.value = message;
          } finally {
            setLoading(false);
            input.focus();
          }
        });

        // === Вспомогательные функции ===

        function addMessage(sender, text, type) {
          const msgDiv = document.createElement('div');
          msgDiv.className = `message ${type}`;
          msgDiv.style.marginBottom = '10px';
          msgDiv.style.padding = '8px 12px';
          msgDiv.style.borderRadius = '6px';
          msgDiv.style.background = type === 'user' ? '#d1e7dd' : '#e2e3e5';

          msgDiv.innerHTML = `<strong>${sender}:</strong> <span>${escapeHtml(text)}</span>`;
          chatBox.appendChild(msgDiv);

          // Автопрокрутка вниз
          chatBox.scrollTop = chatBox.scrollHeight;
        }

        function setLoading(loading) {
          sendBtn.disabled = loading;
          sendBtn.textContent = loading ? 'Отправка...' : 'Отправить';
          input.disabled = loading;
          if (loading) input.placeholder = 'Подождите...';
          else input.placeholder = 'Введите сообщение...';
        }

        function showError(text) {
          errorMsg.textContent = text;
          errorMsg.style.display = 'block';
        }

        function hideError() {
          errorMsg.style.display = 'none';
        }

        // Защита от XSS при выводе текста
        function escapeHtml(text) {
          const div = document.createElement('div');
          div.textContent = text;
          return div.innerHTML;
        }

        // Отправка по Enter
        input.addEventListener('keypress', function(e) {
          if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.requestSubmit();
          }
        });
      });
    </script>
@endsection
