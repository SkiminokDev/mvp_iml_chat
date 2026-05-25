@extends('layouts.admin')

@section('title', 'Клиент #' . $client->id)

@section('content')
    <div class="container mx-auto">
        <div class="breadcrumb flex items-center border-b border-gray-300 pb-4 mb-6">
            <p class="text-xl mr-1 font-semibold">Информация о клиенте</p>
            <ul>
                <li class="border-gray-400">
                    <a href="{{ route('admin.chats.index') }}">Админпанель</a>
                </li>
                <li class="mx-2">/</li>
                <li class="border-gray-400">
                    <a href="{{ route('admin.clients.index') }}">Клиенты</a>
                </li>
                <li class="mx-2">/</li>
                <li class="text-gray-500">{{ $client->name }}</li>
            </ul>
        </div>
    </div>

    <div class="container mx-auto">
        {{-- Основная информация --}}
        <div class="card mb-4 bg-[#fff]">
            <div class="card-header">
                <div class="card-title py-3">Основная информация</div>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">ID</span>
                        </label>
                        <input type="text" value="{{ $client->id }}" readonly 
                               class="input input-bordered w-full bg-base-200">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Имя</span>
                        </label>
                        <input type="text" value="{{ $client->name }}" readonly 
                               class="input input-bordered w-full bg-base-200">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Статус</span>
                        </label>
                        <div class="flex items-center">
                            @if($client->active)
                                <span class="badge badge-success">Активен</span>
                            @else
                                <span class="badge badge-ghost">Неактивен</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Баланс</span>
                        </label>
                        <input type="text" value="{{ number_format($client->balance, 0, '.', ' ') }}" readonly 
                               class="input input-bordered w-full bg-base-200 font-mono">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Дата активности</span>
                        </label>
                        <input type="text" 
                               value="{{ $client->active_data ? $client->active_data->format('d.m.Y H:i') : '—' }}" 
                               readonly class="input input-bordered w-full bg-base-200">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Создан</span>
                        </label>
                        <input type="text" value="{{ $client->created_at->format('d.m.Y H:i') }}" readonly 
                               class="input input-bordered w-full bg-base-200">
                    </div>
                </div>
            </div>
        </div>

        {{-- Чаты клиента --}}
        <div class="card mb-4 bg-[#fff]">
            <div class="card-header">
                <div class="card-title py-3">Чаты клиента</div>
            </div>
            <div class="card-body">
                @if($chats->count() > 0)
                    <div class="dataTable-container">
                        <div class="datatable-wrapper datatable-loading no-footer sortable searchable fixed-columns">
                            <div class="datatable-container">
                                <table class="table-auto datatable-table">
                                    <thead>
                                    <tr>
                                        <th class="w-16">ID</th>
                                        <th class="text-left w-100">Название</th>
                                        <th class="text-center w-30">Статус</th>
                                        <th class="text-right w-40 hidden md:table-cell">Последнее сообщение</th>
                                        <th class="text-right w-50">Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($chats as $chat)
                                            <tr class="hover">
                                                <td class="font-mono text-sm">{{ $chat->id }}</td>
                                                <td>
                                                    <div class="font-medium">{{ $chat->title }}</div>
                                                    @if($chat->description)
                                                        <div class="text-xs text-base-content/60 truncate max-w-xs">
                                                            {{ $chat->description }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($chat->is_active)
                                                        <span class="badge badge-success badge-sm">Активен</span>
                                                    @else
                                                        <span class="badge badge-ghost badge-sm">Архив</span>
                                                    @endif
                                                </td>
                                                <td class="text-right text-sm text-base-content/70 hidden md:table-cell">
                                                    @if($chat->last_message_at)
                                                        {{ $chat->last_message_at->format('d.m.Y H:i') }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    <div class="join">
                                                        <a href="{{ route('admin.chats.index') }}" 
                                                           class="btn btn-ghost btn-xs join-item" 
                                                           title="Открыть чат">
                                                            🔗 Открыть
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-8">
                                                    <p class="text-muted">У клиента нет чатов</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($chats->hasPages())
                        <div class="card-actions justify-end p-4">
                            {{ $chats->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <span class="text-4xl opacity-30">💬</span>
                        <p class="mt-2 text-muted">У клиента пока нет чатов</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Кнопки действий --}}
        <div class="flex justify-end gap-2 mb-4">
            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline">
                ← Назад к списку
            </a>
        </div>
    </div>
@endsection
