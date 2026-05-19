@extends('layouts.user')

@section('title', 'Управление чатами')

@section('content')
    <div class="container mx-auto">
        <div class="breadcrumb flex items-center border-b border-gray-300 pb-4 mb-6">
            <p class="text-xl mr-1 font-semibold">Чаты</p>
            <ul>
                <li class="border-gray-400">
                    <a class="hover:text-gray-800" href="">user id {{$user->id}}</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container mx-auto">
        <div class="card mb-4 bg-[#fff]">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <div class="card-title py-3">Список пользовательских чатов</div>
                    <a href="{{ route('admin.chats.create') }}" class="btn ripple btn-primary mr-2 text-[#fff] rounded-full">
                        Создать чат
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="dataTable-container">
                    <div class="datatable-wrapper datatable-loading no-footer sortable searchable fixed-columns">
                        <div class="datatable-container">
                            <table class="table-auto datatable-table">
                                <thead>
                                <tr>
                                    <th class="w-16">ID</th>
                                    <th class="text-left w-100">Пользователь</th>
                                    <th class="text-left w-100">Название</th>
                                    <th class="text-center w-30">Статус</th>
                                    <th class="text-right w-40 hidden md:table-cell">Создан</th>
                                    <th class="text-right w-50">Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($chats as $chat)
                                    <tr class="hover">
                                        <td class="font-mono text-sm">{{ $chat->id }}</td>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div class="avatar placeholder">
                                                    <div class="bg-neutral-focus text-neutral-content rounded-full w-8">
                                                        <span class="text-xs">{{ $chat->user->email[0] ?? '?' }}</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-medium">{{ $chat->user->email ?? 'N/A' }} ID: {{ $chat->user_id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="font-medium">{{ $chat->title }}</span>
                                            @if($chat->description)
                                                <div class="text-xs text-base-content/50 truncate max-w-[200px]">
                                                    {{ $chat->description }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($chat->is_active)
                                                <span class="badge badge-success badge-sm">Активен</span>
                                            @else
                                                <span class="badge badge-ghost badge-sm">Неактивен</span>
                                            @endif
                                        </td>
                                        <td class="text-right text-sm text-base-content/70 hidden md:table-cell">
                                            {{ $chat->created_at->format('d.m.Y') }} {{ $chat->created_at->format('H:i') }}
                                        </td>
                                        <td class="text-right">
                                            <div class="join">
                                                <button class="btn btn-ghost btn-xs join-item" title="Просмотр">
                                                    👁️
                                                </button>
                                                <button class="btn btn-ghost btn-xs join-item" title="Редактировать">
                                                    ✏️
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-12">
                                            <div class="flex flex-col items-center gap-4">
                                                <span class="text-4xl opacity-30">💬</span>
                                                <div>
                                                    <p class="font-medium">Чатов пока нет</p>
                                                    <p class="text-sm text-base-content/60">Создайте первый чат для начала работы</p>
                                                </div>
                                                <a href="{{ route('admin.chats.create') }}" class="btn btn-primary btn-sm">
                                                    + Создать чат
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- пагинация --}}

            @if($chats->hasPages())
                <div class="card-actions justify-end p-4">
                    {{ $chats->links() }}
                </div>
            @endif
            {{-- // Пагинация --}}
        </div>
    </div>
    @push('scripts')
        <script>
          // Простой поиск по таблице (клиентский)
          document.getElementById('searchInput')?.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(row => {
              const text = row.textContent.toLowerCase();
              row.style.display = text.includes(query) ? '' : 'none';
            });
          });
        </script>
    @endpush
@endsection
