@extends('layouts.admin')

@section('title', 'Управление чатами')

@section('content')
    <div class="container mx-auto">
        <div class="breadcrumb flex items-center border-b border-gray-300 pb-4 mb-6">
            <p class="text-xl mr-1 font-semibold">Чаты</p>
            <ul>
                <li class="border-gray-400">
                    <a class="hover:text-gray-800" href="">Админпанель</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container mx-auto">
        <div class="card mb-4 bg-[#fff]">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <div class="card-title py-3">Список пользовательских чатов</div>
                    <a href="{{ route('admin.users.create') }}" class="btn ripple btn-primary mr-2 text-[#fff] rounded-full">
                        Создать пользователя
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
                                    <th class="text-left w-100">Email</th>
                                    <th class="text-left w-100">Статус</th>
                                    <th class="text-center w-30">Роль</th>
                                    <th class="text-right w-40 hidden md:table-cell">Создан</th>
                                    <th class="text-right w-50">Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($users as $user)
                                    <tr class="hover">
                                        <td class="font-mono text-sm">
                                            {{ $user->id }}
                                        </td>
                                        <td>
                                            <span class="text-xs">{{ $user->email ?? '?' }}</span>
                                        </td>
                                        <td>
                                            <div class="font-medium">
                                                @if($user->is_active)
                                                    <span class="badge bg-success">Активен</span>
                                                @else
                                                    <span class="badge bg-secondary">Неактивен</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->is_admin)
                                                <span class="badge bg-danger">Админ</span>
                                            @else
                                                <span class="badge bg-info">Пользователь</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ $user->created_at?->format('d.m.Y') }}
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
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            Пользователи не найдены
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Пагинация --}}
            <div class="mt-3">
                {{ $users->links() }}
            </div>
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
