<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Админ-панель')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/interface.css') }}">

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-base-200 min-h-screen app-admin-wrap-layout-2 layout-sidebar-large subheader-none">

{{-- Верхняя панель --}}
<div class="header-2-wrapper">

</div>
{{-- Обертка контента --}}
<div class="main-content-wrap">
    {{-- Боковое меню --}}
    <div clas="side-content-wrap">
        <aside class="sidebar-left ps ps--active-y open">
            <ul class="menu navigation-left">
                <li class="nav-item">
                    <a href="#" class="nav-item-hold justify-center">
                        <i class="nav-icon i-Bar-Chart-2 text-base mr-2"></i>
                        <p class="items-center">Дашборд</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.chats.index') }}" class="nav-item-hold justify-center {{ request()->routeIs('admin.chats.*') ? 'active' : '' }}">
                        <i class="nav-icon i-Bar-Chart-2 text-base mr-2"></i>
                        <p class="items-center">Чаты</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-item-hold justify-center">
                        <i class="nav-icon i-Bar-Chart-2 text-base mr-2"></i>
                        <p class="items-center">Профиль</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-item-hold justify-center">
                        <i class="nav-icon i-Bar-Chart-2 text-base mr-2"></i>
                        <p class="items-center">Настройки</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-item-hold justify-center">
                        <i class="nav-icon i-Bar-Chart-2 text-base mr-2"></i>
                        <p class="items-center">Системные сообщения</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-item-hold justify-center">
                        <i class="nav-icon i-Bar-Chart-2 text-base mr-2"></i>
                        <p class="items-center">Тарифы и оплата</p>
                    </a>
                </li>
            </ul>
        </aside>
    </div>

    {{-- Основной контент --}}
    <main class="main-content-body pt-10 px-4 flex flex-col sm:px-8">
        @yield('content')
    </main>
</div>

{{-- Скрытая форма для POST-запроса --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

{{-- Футер --}}
<footer class="footer footer-center p-4 bg-base-100 text-base-content">
    <aside>
        <p>© {{ date('Y') }} LLMBot. Все права защищены.</p>
    </aside>
</footer>

@stack('scripts')
</body>
</html>
