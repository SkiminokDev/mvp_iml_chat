@extends('layouts.admin')

@section('title', 'Сообщения автора: ' . $author->email)

@section('content')
    <div class="container mx-auto">
        <div class="breadcrumb flex items-center border-b border-gray-300 pb-4 mb-6">
            <p class="text-xl mr-1 font-semibold">Сообщения автора</p>
            <ul>
                <li class="border-gray-400">
                    <a href="{{ route('admin.chats.index') }}">Админпанель</a>
                </li>
                <li class="mx-2">/</li>
                <li class="border-gray-400">
                    <a href="{{ route('admin.authors.index') }}">Авторы</a>
                </li>
                <li class="mx-2">/</li>
                <li class="text-gray-500">{{ $author->email }}</li>
            </ul>
        </div>
    </div>

    <div class="container mx-auto">
        <div class="card mb-4 bg-[#fff]">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <div class="card-title py-3">
                        Сообщения автора: <span class="font-bold text-primary">{{ $author->email }}</span> (ID: {{ $author->id }})
                    </div>
                    <a href="{{ route('admin.authors.index') }}" class="btn btn-outline btn-sm">
                        ← Назад к авторам
                    </a>
                </div>
            </div>
            <div class="card-body">
                {{-- Сортировка и пагинация --}}
                <form method="GET" action="{{ route('admin.authors.messages', $author->id) }}" class="mb-4 flex flex-wrap gap-4 items-end">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Сортировать по</span>
                        </label>
                        <select name="sort_by" class="select select-bordered w-full max-w-xs">
                            <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Время создания</option>
                            <option value="id" {{ $sortBy == 'id' ? 'selected' : '' }}>ID</option>
                        </select>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Порядок</span>
                        </label>
                        <select name="sort_order" class="select select-bordered w-full max-w-xs">
                            <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>По убыванию</option>
                            <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>По возрастанию</option>
                        </select>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">На странице</span>
                        </label>
                        <select name="per_page" class="select select-bordered w-full max-w-xs">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                    
                    <div class="form-control">
                        <button type="submit" class="btn btn-primary mt-5">
                            ↻ Применить
                        </button>
                    </div>
                </form>

                <div class="dataTable-container">
                    <div class="datatable-wrapper datatable-loading no-footer sortable searchable fixed-columns">
                        <div class="datatable-container">
                            <table class="table-auto datatable-table">
                                <thead>
                                <tr>
                                    <th class="w-16">ID</th>
                                    <th class="text-left w-100">Чат</th>
                                    <th class="text-left">Текст сообщения</th>
                                    <th class="text-center w-30">Отправитель</th>
                                    <th class="text-center w-30">Прочитано</th>
                                    <th class="text-right w-40 hidden md:table-cell">Создано</th>
                                    <th class="text-right w-40 hidden md:table-cell">Client ID</th>
                                    <th class="text-right w-40 hidden md:table-cell">Type Place</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse($messages as $message)
                                        <tr class="hover">
                                            <td class="font-mono text-sm">{{ $message->id }}</td>
                                            <td>
                                                @if($message->chat)
                                                    <span class="text-sm font-medium">{{ $message->chat->title ?? 'Без названия' }}</span>
                                                @else
                                                    <span class="text-sm text-base-content/50">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="max-w-md truncate" title="{{ $message->text }}">
                                                    {{ Str::limit($message->text, 80) }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($message->sender === 'user')
                                                    <span class="badge badge-info badge-sm">Пользователь</span>
                                                @elseif($message->sender === 'bot')
                                                    <span class="badge badge-success badge-sm">Бот</span>
                                                @else
                                                    <span class="badge badge-ghost badge-sm">{{ $message->sender }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($message->is_read)
                                                    <span class="badge badge-success badge-sm">✓</span>
                                                @else
                                                    <span class="badge badge-ghost badge-sm">○</span>
                                                @endif
                                            </td>
                                            <td class="text-right text-sm text-base-content/70 hidden md:table-cell">
                                                {{ $message->created_at->format('d.m.Y H:i') }}
                                            </td>
                                            <td class="text-right text-sm text-base-content/70 hidden md:table-cell">
                                                {{ $message->client_id ?? '—' }}
                                            </td>
                                            <td class="text-right text-sm text-base-content/70 hidden md:table-cell">
                                                {{ $message->type_place ?? '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-12">
                                                <div class="flex flex-col items-center gap-4">
                                                    <span class="text-4xl opacity-30">💬</span>
                                                    <div>
                                                        <p class="font-medium">Сообщений нет</p>
                                                        <p class="text-sm text-base-content/60">У этого автора пока нет сообщений</p>
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

            @if($messages->hasPages())
                <div class="card-actions justify-end p-4">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
