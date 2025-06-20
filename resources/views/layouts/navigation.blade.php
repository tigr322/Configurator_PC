<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Конфигуратор ПК</title>

    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.png') }}">
   <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/light-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
   
</head>
<body>
    <div class="page-wrapper">
        <header class="navigation">
            @if (Route::has('login'))
                <nav>
                    <a href="{{ url('/') }}">🖥️</a>

                    @auth
                    <button class="button-theme" id="themeToggle">
                        <span class="sun-icon">
                            <!-- SVG для Солнца -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <circle cx="12" cy="12" r="5" fill="yellow" />
                                <line x1="12" y1="0" x2="12" y2="4" stroke="yellow" stroke-width="2"/>
                                <line x1="12" y1="20" x2="12" y2="24" stroke="yellow" stroke-width="2"/>
                                <line x1="0" y1="12" x2="4" y2="12" stroke="yellow" stroke-width="2"/>
                                <line x1="20" y1="12" x2="24" y2="12" stroke="yellow" stroke-width="2"/>
                                <line x1="3" y1="3" x2="6" y2="6" stroke="yellow" stroke-width="2"/>
                                <line x1="18" y1="18" x2="21" y2="21" stroke="yellow" stroke-width="2"/>
                                <line x1="3" y1="21" x2="6" y2="18" stroke="yellow" stroke-width="2"/>
                                <line x1="18" y1="6" x2="21" y2="3" stroke="yellow" stroke-width="2"/>
                            </svg>
                        </span>
                        <span class="moon-icon">
                            <!-- SVG для Луны -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <circle cx="15" cy="9" r="8" fill="darkgray" />
                                <circle cx="17" cy="7" r="6" fill="white" />
                            </svg>
                        </span>
                    </button>
                    
                    
                        <a href="{{ url('/configurator') }}">Конфигуратор</a>
                        <a href="{{ route('profile.editProfile') }}">Профиль</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Выход</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}">Вход</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Регистрация</a>
                        @endif
                    @endauth
                    <a href="{{ route('catalog') }}">Каталог комплектующих</a>
                    <a href="{{ route('builds') }}">Популярные конфигурации</a>
                    @if (auth()->check() && auth()->user()->admin == 1)
                        <a href="{{ route('profile.edit'    ) }}">Администрирование пользователей</a>
                    
                    @endif
                </nav>
            @endif
            <!--<div class="max-w-xl mx-auto mt-6">
                <h2 class="text-lg font-semibold mb-2">🤖 Задать вопрос</h2>
                <textarea id="userMessage" rows="2"
                    class="w-full p-2 text-sm border border-gray-300 rounded mb-2 resize-none"
                    placeholder="Например: сборка до 80 000₽"></textarea>
                <button onclick="sendMessage()"
                    class="bg-blue-600 text-white text-sm px-3 py-1.5 rounded hover:bg-blue-700 transition">
                    Отправить
                </button>
            
                <div id="chatReply" class="mt-3 p-3 bg-gray-50 rounded text-sm text-gray-800 border border-gray-200"></div>
            </div>-->
        </header>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const nav = document.querySelector('.navigation');
            let lastScrollY = window.scrollY;
    
            // Оптимизация: используем requestAnimationFrame для плавности
            let ticking = false;
    
            window.addEventListener('scroll', function () {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        if (window.scrollY > lastScrollY && window.scrollY > 100) {
                            nav.classList.add('hide');
                        } else {
                            nav.classList.remove('hide');
                        }
                        lastScrollY = window.scrollY;
                        ticking = false;
                    });
                    ticking = true;
                }
            });
    
            // Темная/светлая тема
            const toggleButton = document.getElementById("themeToggle");
            const savedTheme = localStorage.getItem("theme");
    
            if (savedTheme === "light") {
                document.body.classList.add("light");
            }
    
            toggleButton?.addEventListener("click", function () {
                document.body.classList.toggle("light");
                localStorage.setItem("theme", document.body.classList.contains("light") ? "light" : "dark");
            });
        });
    </script>
    
  <script>
    const themeToggleButton = document.getElementById('themeToggle');
    const bodyElement = document.body;

    // Устанавливаем тему при загрузке страницы
    const savedTheme = localStorage.getItem('theme') || 'light';
    bodyElement.classList.add(savedTheme + '-theme');

    themeToggleButton.addEventListener('click', () => {
        // Переключаем классы
        bodyElement.classList.toggle('dark-theme');
        bodyElement.classList.toggle('light-theme');

        // Сохраняем тему в localStorage
        const newTheme = bodyElement.classList.contains('dark-theme') ? 'dark' : 'light';
        localStorage.setItem('theme', newTheme);
    });
</script>
<script>
    function sendMessage() {
    const message = document.getElementById('userMessage').value;
    const replyBox = document.getElementById('chatReply');
    replyBox.innerHTML = "⏳ Ответ генерируется...";

    fetch("{{ route('ai.chat') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ message })
    })
    .then(res => res.json())
    .then(data => {
        replyBox.innerHTML = data.reply;
    })
    .catch(() => {
        replyBox.innerHTML = "⚠️ Ошибка при получении ответа.";
    });
}
</script>
</body>
</html>
