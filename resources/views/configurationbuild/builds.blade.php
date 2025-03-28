<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог конфигурации</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>

    </style>
</head>

<body>
@include('layouts.navigation')  

<div class="container mx-auto px-4 py-3">
<form method="GET" action="{{ route('configurations') }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
    <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" type="text" name="search" placeholder="Название" value="{{ request('search') }}" class="border p-2 rounded">
    <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" type="text" name="component" placeholder="Компонент" value="{{ request('component') }}" class="border p-2 rounded">
    <select style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" name="sort" class="border p-2 rounded">
        <option value="">Сортировка</option>
        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Цена ↑</option>
        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Цена ↓</option>
    </select>
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded col-span-1 md:col-span-2">Применить</button>
</form>

<div class="grid grid-cols-2 md:grid-cols-1 lg:grid-cols-2 gap-6">
          
    @forelse ($builds as $build)
        <div class="border rounded-lg p-4 shadow">
           
            <h2 class="text-lg font-semibold">{{ $build->name }}</h2>
            <p class="text-sm text-gray-500">{{ $build->total_price}}</p>
            <a href="{{ route('configurationbuild.showconf', $build->id) }}" class="inline-block mt-2 text-blue-500 hover:underline">
                Подробнее
            </a>
            
        </div>
    @empty
        <p>Комплектующие не найдены.</p>
    @endforelse
</div>
</div>

    <script>
        function selectBuild(selectElement) {
            const selectedId = selectElement.value;
            if (selectedId) {
                window.location.href = "/configurations/" + selectedId;
            }
        }
    </script>
</body>
</html>

