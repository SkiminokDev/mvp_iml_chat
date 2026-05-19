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
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="form-control">
                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Пароль *</label>
                <input type="password" name="password" required class="form-control">
                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Подтверждение пароля *</label>
                <input type="password" name="password_confirmation" required class="form-control">
            </div>

            <div class="form-check">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="form-check-input">
                <label class="form-check-label">Активен</label>
            </div>

            <div class="form-check">
                <input type="checkbox" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }} class="form-check-input">
                <label class="form-check-label">Администратор</label>
            </div>

            <!-- Внешние ключи -->
            <div class="form-group">
                <label>Профиль</label>
                <select name="profile_id" class="form-control">
                    <option value="">Без профиля</option>
                    @foreach($profiles as $profile)
                        <option value="{{ $profile->id }}" {{ old('profile_id') == $profile->id ? 'selected' : '' }}>
                            {{ $profile->name ?? $profile->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Создать</button>
        </form>
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
