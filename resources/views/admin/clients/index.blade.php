@extends('layouts.admin')

@section('title', 'Клиенты')

@section('content')
    <div class="container mx-auto">
        <div class="breadcrumb flex items-center border-b border-gray-300 pb-4 mb-6">
            <p class="text-xl mr-1 font-semibold">Клиенты</p>
            <ul>
                <li class="border-gray-400">
                    <a href="{{ route('admin.chats.index') }}">Админпанель</a>
                </li>
                <li class="mx-2">/</li>
                <li class="text-gray-500">Клиенты</li>
            </ul>
        </div>
    </div>

    <div class="container mx-auto">
        <div class="card mb-4 bg-[#fff]">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <div class="card-title py-3">Список клиентов</div>
                </div>
            </div>
            <div class="card-body">
                {{-- Фильтры --}}
                <form method="GET" action="{{ route('admin.clients.index') }}" class="mb-4 flex flex-wrap gap-4 items-end">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">ID клиента</span>
                        </label>
                        <input type="text" name="filter_id" value="{{ $filterId ?? '' }}" 
                               placeholder="Например: 1" 
                               class="input input-bordered w-full max-w-xs">
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Имя</span>
                        </label>
                        <input type="text" name="filter_name" value="{{ $filterName ?? '' }}" 
                               placeholder="Поиск по имени" 
                               class="input input-bordered w-full max-w-xs">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Статус</span>
                        </label>
                        <select name="filter_active" class="select select-bordered w-full max-w-xs">
                            <option value="">Все</option>
                            <option value="1" {{ ($filterActive ?? '') == '1' ? 'selected' : '' }}>Активен</option>
                            <option value="0" {{ ($filterActive ?? '') === '0' ? 'selected' : '' }}>Неактивен</option>
                        </select>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">На странице</span>
                        </label>
                        <select name="per_page" class="select select-bordered w-full max-w-xs">
                            <option value="10" {{ ($perPage ?? 20) == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ ($perPage ?? 20) == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ ($perPage ?? 20) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($perPage ?? 20) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                    
                    <div class="form-control">
                        <button type="submit" class="btn btn-primary mt-5">
                            🔍 Фильтр
                        </button>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline mt-5 ml-2">
                            ✕ Сброс
                        </a>
                    </div>
                </form>

                <div class="dataTable-container">
                    <div class="datatable-wrapper datatable-loading no-footer sortable searchable fixed-columns">
                        <div class="datatable-container">
                            <table class="table-auto datatable-table">
                                <thead>
                                <tr>
                                    <th class="w-16">ID</th>
                                    <th class="text-left w-100">Имя</th>
                                    <th class="text-center w-30">Статус</th>
                                    <th class="text-center w-30">Баланс</th>
                                    <th class="text-right w-40 hidden md:table-cell">Дата активности</th>
                                    <th class="text-right w-50">Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse($clients as $client)
                                        <tr class="hover">
                                            <td class="font-mono text-sm">{{ $client->id }}</td>
                                            <td>
                                                <div class="flex items-center gap-3">
                                                    <div class="avatar placeholder">
                                                        <div class="bg-neutral-focus text-neutral-content rounded-full w-8">
                                                            <span class="text-xs">{{ strtoupper(substr($client->name, 0, 1)) }}</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium">{{ $client->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($client->active)
                                                    <span class="badge badge-success badge-sm">Активен</span>
                                                @else
                                                    <span class="badge badge-ghost badge-sm">Неактивен</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="font-mono">{{ number_format($client->balance, 0, '.', ' ') }}</span>
                                            </td>
                                            <td class="text-right text-sm text-base-content/70 hidden md:table-cell">
                                                @if($client->active_data)
                                                    {{ $client->active_data->format('d.m.Y') }} {{ $client->active_data->format('H:i') }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div class="join">
                                                    <a href="{{ route('admin.clients.show', $client->id) }}" 
                                                       class="btn btn-ghost btn-xs join-item" 
                                                       title="Информация о клиенте">
                                                        👁️ Просмотр
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-12">
                                                <div class="flex flex-col items-center gap-4">
                                                    <span class="text-4xl opacity-30">👤</span>
                                                    <div>
                                                        <p class="font-medium">Клиентов не найдено</p>
                                                        <p class="text-sm text-base-content/60">Попробуйте изменить параметры фильтра</p>
                                                    </div>
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

            @if($clients->hasPages())
                <div class="card-actions justify-end p-4">
                    {{ $clients->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
