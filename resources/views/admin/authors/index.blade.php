@extends('layouts.admin')

@section('title', 'Авторы сообщений')

@section('content')
    <div class="container mx-auto">
        <div class="breadcrumb flex items-center border-b border-gray-300 pb-4 mb-6">
            <p class="text-xl mr-1 font-semibold">Авторы сообщений</p>
            <ul>
                <li class="border-gray-400">
                    <a href="{{ route('admin.chats.index') }}">Админпанель</a>
                </li>
                <li class="mx-2">/</li>
                <li class="text-gray-500">Авторы</li>
            </ul>
        </div>
    </div>

    <div class="container mx-auto">
        <div class="card mb-4 bg-[#fff]">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <div class="card-title py-3">Список авторов сообщений</div>
                </div>
            </div>
            <div class="card-body">
                {{-- Фильтры --}}
                <form method="GET" action="{{ route('admin.authors.index') }}" class="mb-4 flex flex-wrap gap-4 items-end">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">ID автора</span>
                        </label>
                        <input type="text" name="filter_id" value="{{ $filterId ?? '' }}" 
                               placeholder="Например: 1" 
                               class="input input-bordered w-full max-w-xs">
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Email (название)</span>
                        </label>
                        <input type="text" name="filter_name" value="{{ $filterName ?? '' }}" 
                               placeholder="Поиск по email" 
                               class="input input-bordered w-full max-w-xs">
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
                        <a href="{{ route('admin.authors.index') }}" class="btn btn-outline mt-5 ml-2">
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
                                    <th class="text-left w-100">Email</th>
                                    <th class="text-center w-30">Статус</th>
                                    <th class="text-center w-30">Админ</th>
                                    <th class="text-right w-40 hidden md:table-cell">Зарегистрирован</th>
                                    <th class="text-right w-50">Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse($authors as $author)
                                        <tr class="hover">
                                            <td class="font-mono text-sm">{{ $author->id }}</td>
                                            <td>
                                                <div class="flex items-center gap-3">
                                                    <div class="avatar placeholder">
                                                        <div class="bg-neutral-focus text-neutral-content rounded-full w-8">
                                                            <span class="text-xs">{{ $author->email[0] ?? '?' }}</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium">{{ $author->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($author->is_active)
                                                    <span class="badge badge-success badge-sm">Активен</span>
                                                @else
                                                    <span class="badge badge-ghost badge-sm">Неактивен</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($author->is_admin)
                                                    <span class="badge badge-warning badge-sm">Да</span>
                                                @else
                                                    <span class="badge badge-ghost badge-sm">Нет</span>
                                                @endif
                                            </td>
                                            <td class="text-right text-sm text-base-content/70 hidden md:table-cell">
                                                {{ $author->created_at->format('d.m.Y') }} {{ $author->created_at->format('H:i') }}
                                            </td>
                                            <td class="text-right">
                                                <div class="join">
                                                    <a href="{{ route('admin.authors.messages', $author->id) }}" 
                                                       class="btn btn-ghost btn-xs join-item" 
                                                       title="Сообщения автора">
                                                        📝 Сообщения
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
                                                        <p class="font-medium">Авторов не найдено</p>
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

            @if($authors->hasPages())
                <div class="card-actions justify-end p-4">
                    {{ $authors->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
