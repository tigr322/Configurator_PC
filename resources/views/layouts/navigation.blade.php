<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Configurator PC-Market</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <!-- -->
<header  class="navigation">
    @if (Route::has('login'))
        <nav class="flex items-center justify-end gap-4">
            <a style="color: aliceblue" href="{{ url('/') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pc-display-horizontal" viewBox="0 0 16 16">
                    <path d="M1.5 0A1.5 1.5 0 0 0 0 1.5v7A1.5 1.5 0 0 0 1.5 10H6v1H1a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1h-5v-1h4.5A1.5 1.5 0 0 0 16 8.5v-7A1.5 1.5 0 0 0 14.5 0h-13Zm0 1h13a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .5-.5ZM12 12.5a.5.5 0 1 1 1 0 .5.5 0 0 1-1 0Zm2 0a.5.5 0 1 1 1 0 .5.5 0 0 1-1 0ZM1.5 12h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1ZM1 14.25a.25.25 0 0 1 .25-.25h5.5a.25.25 0 1 1 0 .5h-5.5a.25.25 0 0 1-.25-.25Z"/>
                </svg>
            </a>
            @auth
                <a href="{{ url('/dashboard') }}" class="inline-block px-5 py-1.5 border border-[#3E3E3A] rounded-sm text-sm leading-normal">
                    Магазин
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-block px-5 py-1.5 border border-transparent hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                        Выход
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="inline-block px-5 py-1.5 border border-transparent hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                    Вход
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-block px-5 py-1.5 border border-transparent hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                        Регистрация
                    </a>
                @endif
            @endauth
            
            <a href="{{ route('catalog') }}">
                Каталог комплектующих
            </a>
            <a href="{{ route('builds') }}">
                Популярные конфигурации
            </a>
        </nav>
    @endif
</header>
<main>
    @yield('content')
</main>

<footer>
    <p>© 2025 Конфигуратор ПК</p>
</footer>
</body>
</html>