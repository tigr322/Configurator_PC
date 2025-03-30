<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог комплектующих</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>

    </style>
</head>

<body>
    @include('layouts.navigation')
   
    
    <div class="container mx-auto px-4 py-3">
        @if (auth()->check() && auth()->user()->admin == 1)
    <div class="mb-8 p-4 border rounded bg-black-100">
        <h2 class="text-xl font-semibold mb-4">🔧 Админ-панель: Парсинг комплектующих</h2>
        <form method="POST" action="{{ route('admin.parse') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="category_id" class="block mb-1 text-sm font-medium">Категория</label>
                    <select style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black; name="category_id" id="category_id" required
                            class="w-full border rounded px-3 py-2 bg-white text-black">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="source_url" class="block mb-1 text-sm font-medium">URL источника</label>
                    <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black; type="url" name="source_url" id="source_url" placeholder="https://..."
                           class="w-full border rounded px-3 py-2 bg-white text-black">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Начать парсинг
                    </button>
                </div>
            </div>
        </form>
    </div>
@endif
        <h1 class="text-3xl font-bold mb-6">Каталог комплектующих</h1>
        
        {{-- Форма фильтрации --}}
        <form method="GET" action="{{ route('catalog') }}" class="mb-6 grid grid-cols-4 md:grid-cols-3 gap-4">
            <select style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" name="category" class="border p-2 rounded">
                <option value="">Все категории</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->name }}" {{ request('category') === $category->name ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;"  type="text" name="name" placeholder="Название" value="{{ request('name') }}" class="border p-2 rounded">

            <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;"  type="text" name="brand" placeholder="Бренд" value="{{ request('brand') }}" class="border p-2 rounded">

            <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" type="number" name="min_price" placeholder="Мин. цена" value="{{ request('min_price') }}" class="border p-2 rounded">
            <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" type="number" name="max_price" placeholder="Макс. цена" value="{{ request('max_price') }}" class="border p-2 rounded">
            <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;"
            type="number" name="pagination" placeholder="Пагинация" value="{{ request('pagination') }}" class="border p-2 rounded">
            <select style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" name="sort" class="border p-2 rounded">
                <option value="">Сортировка</option>
                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Цена ↑</option>
                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Цена ↓</option>
            </select>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded col-span-1 md:col-span-2">Применить</button>
        </form>
        
      
        {{-- Список компонентов --}}
        <div class="grid grid-cols-2 md:grid-cols-1 lg:grid-cols-2 gap-6">
          
            @forelse ($components as $component)
                <div class="border rounded-lg p-4 shadow">
                    @if ($component->image_url)
                        <img src="{{ $component->image_url }}" alt="{{ $component->name }}" class="w-full h-40 object-contain mb-2">
                    @endif
                    <h2 class="text-lg font-semibold">{{ $component->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $component->brand }}</p>
                    <p class="font-bold text-green-600 mt-2">{{ number_format($component->price, 2) }} $</p>
                    <a href="{{ route('components.show', $component->id) }}" class="inline-block mt-2 text-blue-500 hover:underline">
                        Подробнее
                    </a>
                </div>
            @empty
                <p>Комплектующие не найдены.</p>
            @endforelse
        </div>

        {{--Пагинация --}}
        <div class="mt-6">
            {{ $components->withQueryString()->links() }}
        </div>
    </div>
</body>
</html>
