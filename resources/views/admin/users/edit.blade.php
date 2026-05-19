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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>✏️ Редактировать пользователя</h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">← Назад</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email"
                               name="email"
                               id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}"
                               required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Новый пароль</label>
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Оставьте пустым, чтобы не менять">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted">Минимум 8 символов</small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="form-control">
                    </div>

                    {{-- Статусы --}}
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox"
                               name="is_active"
                               id="is_active"
                               class="form-check-input"
                               value="1"
                                {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Активен</label>
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox"
                               name="is_admin"
                               id="is_admin"
                               class="form-check-input"
                               value="1"
                                {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_admin">Права администратора</label>
                    </div>

                    {{-- Внешние ключи --}}
                    <div class="mb-3">
                        <label for="profile_id" class="form-label">Профиль</label>
                        <select name="profile_id" id="profile_id" class="form-select">
                            <option value="">— Без профиля —</option>
                            @foreach($profiles as $profile)
                                <option value="{{ $profile->id }}"
                                        {{ old('profile_id', $user->profile_id) == $profile->id ? 'selected' : '' }}>
                                    {{ $profile->name ?? 'Профиль #' . $profile->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="payment_settings_id" class="form-label">Настройки оплаты</label>
                        <select name="payment_settings_id" id="payment_settings_id" class="form-select">
                            <option value="">— По умолчанию —</option>
                            @foreach($paymentSettings as $setting)
                                <option value="{{ $setting->id }}"
                                        {{ old('payment_settings_id', $user->payment_settings_id) == $setting->id ? 'selected' : '' }}>
                                    {{ $setting->name ?? 'Настройка #' . $setting->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="llm_settings_id" class="form-label">LLM настройки</label>
                        <select name="llm_settings_id" id="llm_settings_id" class="form-select">
                            <option value="">— По умолчанию —</option>
                            @foreach($llmSettings as $setting)
                                <option value="{{ $setting->id }}"
                                        {{ old('llm_settings_id', $user->llm_settings_id) == $setting->id ? 'selected' : '' }}>
                                    {{ $setting->name ?? 'Настройка #' . $setting->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="data_settings_id" class="form-label">Настройки данных</label>
                        <select name="data_settings_id" id="data_settings_id" class="form-select">
                            <option value="">— По умолчанию —</option>
                            @foreach($dataSettings as $setting)
                                <option value="{{ $setting->id }}"
                                        {{ old('data_settings_id', $user->data_settings_id) == $setting->id ? 'selected' : '' }}>
                                    {{ $setting->name ?? 'Настройка #' . $setting->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">💾 Сохранить</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Отмена</a>

                        {{-- Кнопка удаления (отдельно) --}}
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('Удалить пользователя {{$user->email}}?')"
                              class="ms-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">🗑️ Удалить</button>
                        </form>
                    </div>
                </form>
            </div>
        </div>

        {{-- Информация о пользователе --}}
        <div class="card mt-4">
            <div class="card-header">📋 Информация</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9">{{ $user->id }}</dd>

                    <dt class="col-sm-3">Email verified</dt>
                    <dd class="col-sm-9">{{ $user->email_verified_at ?? 'Нет' }}</dd>

                    <dt class="col-sm-3">Создан</dt>
                    <dd class="col-sm-9">{{ $user->created_at?->format('d.m.Y H:i') }}</dd>

                    <dt class="col-sm-3">Обновлён</dt>
                    <dd class="col-sm-9">{{ $user->updated_at?->format('d.m.Y H:i') }}</dd>
                </dl>
            </div>
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
