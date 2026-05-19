@extends('layouts.admin')

@section('title', 'Создать чат')

@section('content')
    <div class="max-w-2xl mx-auto">

        {{-- Хлебные крошки --}}
        <div class="breadcrumbs text-sm mb-6">
            <ul>
                <li><a href="{{ route('admin.chats.index') }}" class="hover:text-primary">Чаты</a></li>
                <li>Создать</li>
            </ul>
        </div>

        {{-- Заголовок --}}
        <div class="flex items-center gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-bold">Создать новый чат</h1>
                <p class="text-base-content/70 text-sm">Заполните форму для создания чата пользователю</p>
            </div>
        </div>

        {{-- Уведомления --}}
        @if(session('success'))
            <div class="alert alert-success shadow-lg mb-6">
                <span>✅ {{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error shadow-lg mb-6">
                <span>❌ {{ session('error') }}</span>
            </div>
        @endif

        {{-- Карточка формы --}}
        <div class="card bg-base-100 shadow-xl text-slate-800 p-5">
            <div class="card-body">
                <form action="{{ route('admin.chats.store') }}" method="POST">
                    @csrf

                     Поле: Пользователь
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text font-medium">Пользователь <span class="text-error">*</span></span>
                        </label>
                        <input
                                type="text"
                                name="user_id"
                                value=""
                                placeholder="id клиента"
                                class="shadow appearance-none border border-slate-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                maxlength="255"
                        />
                        @error('user_id')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                     Поле: Название чата
                    <div class="form-control w-full mt-4">
                        <label class="label">
                            <span class="label-text font-medium">Название чата <span class="text-error">*</span></span>
                        </label>
                        <input
                                type="text"
                                name="title"
                                value="{{ old('title') }}"
                                placeholder="Например: Поддержка, Заказ #123"
                                class="shadow appearance-none border border-slate-400 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                maxlength="255"
                        />
                        @error('title')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                     Поле: Активность
                    <div class="form-control mt-4">
                        <label class="label cursor-pointer justify-start gap-4">
                            <input
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active') ? 'checked' : '' }}
                                    class="checkbox checkbox-primary"
                            />
                            <div>
                                <span class="label-text font-medium">Чат активен</span>
                                <p class="text-xs text-base-content/60">Неактивные чаты скрыты у пользователя</p>
                            </div>
                        </label>
                    </div>

                     Кнопки
                    <div class="card-actions justify-end mt-6 gap-3">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 m-10 rounded focus:outline-none focus:shadow-outline">
                            <span>Создать чат</span>
                            <span class="loading loading-spinner loading-sm hidden" id="submitLoader"></span>
                        </button>
                        <a href="{{ route('admin.chats.index') }}"
                           class="btn btn-ghost m-10"
                        >Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- В начале формы create.blade.php --}}
    @if ($errors->any())
        <div class="alert alert-error mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @push('scripts')
        <script>
          // Простой лоадер при отправке
          document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('submitLoader').classList.remove('hidden');
            document.querySelector('button[type="submit"]').disabled = true;
          });
        </script>
    @endpush
@endsection