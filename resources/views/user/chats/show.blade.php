@extends('layouts.user')

@section('title', 'Чат')

@section('content')
    <div class="container mx-auto max-w-7xl">
        <div class="ul-box-wrapper flex h-[calc(100vh-120px)] border rounded-lg overflow-hidden shadow-lg">

            {{-- 🔹 Сайдбар со списком чатов --}}
            <div class="ul-box-sidebar bg-white w-80 flex-shrink-0 border-r flex flex-col">
                <div class="ul-box-header shadow p-4">
                    <input
                            class="ul-form-input mt-0 rounded-full px-4 py-2 w-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            type="text"
                            id="chatSearch"
                            placeholder="Поиск чатов...">
                </div>

                <div class="ul-box-sidebar-content flex-1 overflow-y-auto">
                    <div class="divide-y divide-gray-200 divide-solid">
                        @forelse($chatList as $item)
                            <a href="{{ route('user.chats.show', $item->id) }}"
                               class="flex items-center justify-between p-3 cursor-pointer hover:bg-gray-100 transition {{ $chat->id === $item->id ? 'bg-primary-50 border-l-4 border-primary-500' : '' }}"
                               data-chat-id="{{ $item->id }}">
                                <div class="flex items-center min-w-0">
                                    <img class="avatar w-10 h-10 mr-3 rounded-full object-cover flex-shrink-0"
                                         src="{{ $item->avatar ?? asset('assets/images/faces/default.jpg') }}"
                                         alt="avatar">
                                    <p class="overflow-ellipsis overflow-hidden whitespace-nowrap flex-1 text-sm font-medium text-gray-800">
                                        Чат #{{ $item->id }}
                                    </p>
                                </div>
                                <span class="badge-status w-2 h-2 rounded-full {{ $item->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                            </a>
                        @empty
                            <div class="p-4 text-center text-gray-500 text-sm">
                                Нет активных чатов
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 🔹 Основная область чата --}}
            <div class="ul-box-container bg-white flex-1 flex flex-col">

                {{-- Заголовок чата --}}
                <div class="ul-box-container-header flex shadow items-center p-4 border-b bg-white">
                    <button id="toggleSidebar" class="ul-box-menu-bar mr-3 lg:hidden text-gray-600 hover:text-gray-900">
                        <span class="material-icons">menu</span>
                    </button>
                    <div class="flex items-center">
                        <img class="avatar w-10 h-10 mr-3 rounded-full object-cover"
                             src="{{ $chat->avatar ?? asset('assets/images/faces/default.jpg') }}"
                             alt="current chat">
                        <div>
                            <span class="font-semibold text-gray-800 block">Чат #{{ $chat->id }}</span>
                            <span class="text-xs text-gray-500 {{ $chat->is_active ? 'text-green-600' : '' }}">
                            {{ $chat->is_active ? '● Онлайн' : '○ Офлайн' }}
                        </span>
                        </div>
                    </div>
                </div>

                {{-- Область сообщений --}}
                <div id="chatMessages" class="ul-box-container-content p-5 flex-1 overflow-y-auto space-y-4 bg-gray-50">
                    @forelse($chat->messages as $message)
                        @if($message->sender === 'user')
                            {{-- Сообщение пользователя (справа) --}}
                            <div class="flex justify-end">
                                <div class="max-w-[70%]">
                                    <div class="p-3 bg-primary-500 text-white rounded-2xl rounded-tr-sm break-words shadow">
                                        <p class="text-sm">{{ e($message->text) }}</p>
                                    </div>
                                    <span class="text-xs text-gray-400 mt-1 block text-right">
                                    {{ $message->created_at?->format('H:i') }}
                                </span>
                                </div>
                            </div>
                        @else
                            {{-- Ответ бота/админа (слева) --}}
                            <div class="flex justify-start">
                                <div class="max-w-[70%]">
                                    <div class="p-3 bg-white border border-gray-200 rounded-2xl rounded-tl-sm break-words shadow-sm">
                                        <p class="text-sm text-gray-800">{{ e($message->response ?? $message->text) }}</p>
                                    </div>
                                    <span class="text-xs text-gray-400 mt-1 block">
                                    {{ $message->created_at?->format('H:i') }}
                                </span>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="flex items-center justify-center h-full text-gray-400">
                            <p>Нет сообщений. Начните диалог! 💬</p>
                        </div>
                    @endforelse
                </div>

                {{-- Форма отправки сообщения --}}
                <div class="ul-box-container-footer p-4 shadow bg-white border-t">
                    <form id="chatForm" class="flex items-end gap-3">
                        @csrf
                        <input type="hidden" name="chat_id" value="{{ $chat->id }}">

                        <div class="flex-1">
                        <textarea
                                id="messageInput"
                                name="text"
                                class="ul-form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"
                                rows="2"
                                placeholder="Напишите сообщение..."
                                required
                        ></textarea>
                        </div>

                        <button
                                type="submit"
                                id="sendBtn"
                                class="btn-icon btn-primary bg-primary-500 hover:bg-primary-600 text-white rounded-full p-3 shadow transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span class="material-icons">send</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Скрипты для чата (подключаются в конце) --}}
    @push('scripts')
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            const chatId = {{ $chat->id }};
            const messagesContainer = document.getElementById('chatMessages');
            const chatForm = document.getElementById('chatForm');
            const messageInput = document.getElementById('messageInput');
            const sendBtn = document.getElementById('sendBtn');

            // 📜 Прокрутка вниз при загрузке
            function scrollToBottom() {
              messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
            scrollToBottom();

            // ✉️ Отправка сообщения через AJAX
            chatForm.addEventListener('submit', async function(e) {
              e.preventDefault();

              const text = messageInput.value.trim();
              if (!text) return;

              // 🔐 Получаем CSRF-токен
              const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
              if (!csrfToken) {
                console.error('CSRF token not found!');
                return;
              }

              sendBtn.disabled = true;
              const originalBtnHtml = sendBtn.innerHTML;
              sendBtn.innerHTML = '<span class="material-icons animate-spin">refresh</span>';

              try {
                const response = await fetch('/api/user/messages/send', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,        // 🔥 CSRF-токен
                    'X-Requested-With': 'XMLHttpRequest' // 🔥 Laravel ждёт этот заголовок
                  },
                  credentials: 'same-origin',            // 🔥 Отправляем cookies (сессия)
                  body: JSON.stringify({
                    text: text,
                    chat_id: chatId
                  })
                });

                if (!response.ok) {
                  // 🚨 Обрабатываем 419 и другие ошибки
                  if (response.status === 419) {
                    throw new Error('Сессия истекла. Обновите страницу.');
                  }
                  const errorData = await response.json().catch(() => ({}));
                  throw new Error(errorData.message || `HTTP ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                  appendMessage(text, 'user', new Date().toLocaleTimeString('ru-RU', {hour: '2-digit', minute:'2-digit'}));

                  if (result.data?.response) {
                    setTimeout(() => {
                      appendMessage(result.data.response, 'bot', new Date().toLocaleTimeString('ru-RU', {hour: '2-digit', minute:'2-digit'}));
                      scrollToBottom();
                    }, 300);
                  }

                  messageInput.value = '';
                  scrollToBottom();
                }
              } catch (error) {
                console.error('Ошибка отправки:', error);
                alert(error.message);
              } finally {
                sendBtn.disabled = false;
                sendBtn.innerHTML = originalBtnHtml;
              }
            });

            // 🎨 Функция добавления сообщения в DOM
            function appendMessage(text, sender, time) {
              const isUser = sender === 'user';
              const messageHtml = `
            <div class="flex ${isUser ? 'justify-end' : 'justify-start'} animate-fade-in">
                <div class="max-w-[70%]">
                    <div class="p-3 ${isUser ? 'bg-primary-500 text-white rounded-2xl rounded-tr-sm' : 'bg-white border border-gray-200 rounded-2xl rounded-tl-sm'} break-words shadow">
                        <p class="text-sm">${escapeHtml(text)}</p>
                    </div>
                    <span class="text-xs text-gray-400 mt-1 block ${isUser ? 'text-right' : ''}">
                        ${time}
                    </span>
                </div>
            </div>
        `;
              messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
            }

            // 🔒 Экранирование HTML
            function escapeHtml(text) {
              const div = document.createElement('div');
              div.textContent = text;
              return div.innerHTML;
            }

            // 🔍 Поиск по чатам в сайдбаре
            const searchInput = document.getElementById('chatSearch');
            if (searchInput) {
              searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('[data-chat-id]').forEach(item => {
                  const text = item.textContent.toLowerCase();
                  item.style.display = text.includes(term) ? '' : 'none';
                });
              });
            }

            // 📱 Адаптив: переключение сайдбара на мобильных
            const toggleBtn = document.getElementById('toggleSidebar');
            const sidebar = document.querySelector('.ul-box-sidebar');
            if (toggleBtn && sidebar) {
              toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('absolute');
                sidebar.classList.toggle('z-50');
                sidebar.classList.toggle('h-full');
              });
            }
          });
        </script>

        {{-- Анимация появления сообщений --}}
        <style>
            @keyframes fade-in {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in {
                animation: fade-in 0.2s ease-out;
            }
        </style>
    @endpush
@endsection
