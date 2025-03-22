<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('layouts.navigation')

        <body class="flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <div class="main-container">
            <h1 class="main-title">Собери свой идеальный ПК</h1>
            <p class="main-subtitle">Выберите лучшие комплектующие и создайте мощный ПК! Для того, чтобы это сделать необходимо авторизоваться!</p>
            <a href="{{ url('/configurator') }}" class="btn-start">Начать сборку</a>
            
        </div>
    </body>
</html>
