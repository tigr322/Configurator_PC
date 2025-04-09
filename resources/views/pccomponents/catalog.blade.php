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
        <div class=" bg-gray-50 border rounded shadow-sm p-4 text-sm">
            <h2 class="text-3xl font-bold mb-6">🔧 Админ-панель</h2>
        
            <!-- Добавление категории -->
            <button class="accordion w-full bg-green-600 text-white py-1.5 text-sm rounded hover:bg-green-700 transition">
                ➕ Категория
            </button>
            <div class="panel hidden mt-2">
                <form method="POST" action="{{ route('admin.addCategory') }}">
                    @csrf
                    <div class="mb-2">
                        <label for="category_name" class="block   mb-1">Новая категория</label>
                        <input type="text" name="category_name" id="category_name" required
                            class="w-full px-2 py-1 border rounded text-black   bg-white">
                    </div>
                    <button type="submit"
                        class="w-full bg-green-600 text-white py-1 rounded   hover:bg-green-700 transition">
                        Добавить
                    </button>
                </form>
            </div>
        
            <!-- Парсинг -->
            <button class="accordion w-full mt-4 bg-green-600 text-white py-1.5 text-sm rounded hover:bg-green-700 transition">
                🔍 Парсинг
            </button>
            <div class="panel hidden mt-2">
                <form method="POST" action="{{ route('admin.parse') }}">
                    @csrf
                    <div class="mb-2">
                        <label for="category_id" class="block   mb-1">Категория</label>
                        <select id="category_id" name="category_id" required
                            class="w-full px-2 py-1 border rounded text-black   bg-white">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="source_url" class="block   mb-1">URL источника</label>
                        <input type="url" name="source_url" id="source_url" placeholder="https://..."
                            class="w-full px-2 py-1 border rounded text-black   bg-white" required>
                    </div>
                    <button type="submit"
                        class="w-full bg-green-600 text-white py-1 rounded   hover:bg-green-700 transition">
                        Начать парсинг
                    </button>
                </form>
            </div>
            <button class="accordion w-full mt-4 bg-green-600 text-white py-1.5 text-sm rounded hover:bg-green-700 transition">
                ✏ Добавить комплектующие в ручную 
            </button>
            <div class="panel hidden mt-2">
                <form method="POST" action="{{ route('admin.addComponent') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2">
                        <label for="category_id" class="block   mb-1">Категория</label>
                        <select id="category_id" name="category_id" required
                            class="w-full px-2 py-1 border rounded text-black   bg-white">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <div class="mb-2">
                        <label for="component_name" class="block   mb-1">Название</label>
                        <input type="text" name="component_name" id="component_name" class="w-full px-2 py-1 border rounded text-black   bg-white" required>
                    </div>
                    <div class="mb-2">
                        <label for="component_price" class="block   mb-1">Цена (руб)</label>
                        <input type="number" name="component_price" id="component_price" class="w-full px-2 py-1 border rounded text-black   bg-white" required>
                    </div>
                    <div class="mb-2">
                        <label for="component_brand" class="block   mb-1">Бренд</label>
                        <input type="text" name="component_brand" id="component_brand" class="w-full px-2 py-1 border rounded text-black   bg-white" required>
                    </div>
            
                    <div class="mb-2">
                        <label for="component_market_url" class="block   mb-1">Ссылка на товар</label>
                        <input type="url" name="component_market_url" id="component_market_url" class="w-full px-2 py-1 border rounded text-black   bg-white" required>
                    </div>
            
                    <div class="mb-2">
                        <label class="block font-semibold   mb-1">Совместимость (JSON)</label>
                        <textarea name="compatibility_data"
                                  class="w-full border p-2 rounded h-28   bg-gray-100 text-black resize-none"
                                  placeholder='{"socket": "AM4", "form_factor": "ATX"}'></textarea>
                    </div>
            
                    <div class="mb-3">
                        <label for="component_image" class="block   mb-1">Изображение</label>
                        <input type="file" name="component_image" id="component_image"
                               class="w-full border rounded   px-2 py-1 bg-white text-black file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:  file:bg-green-600 file:text-white hover:file:bg-green-700">
                    </div>
            
                    <button type="submit"
                        class="w-full bg-green-600 text-white py-1 rounded   hover:bg-green-700 transition">
                        Добавить
                    </button>
                </form>
            </div>
            
        </div>
        
    @endif
    @if (session('success'))
    <div style="color: green; font-weight: bold; text-align: center; margin-top: 1rem;">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div style="color: red; font-weight: bold; text-align: center; margin-top: 1rem;">
        <ul style="list-style-type: none; padding: 0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <h1 class="text-3xl font-bold mb-6">Каталог комплектующих</h1>
        
        {{-- Форма фильтрации --}}
        <form method="GET" action="{{ route('catalog') }}" class="mb-6 grid grid-cols-6 md:grid-cols-4 gap-4">
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
            <form method="POST" action="{{ route('delete', $component->id) }}" >
                @csrf
                @method('DELETE')
                <div class="border rounded-lg p-4 shadow">
                    <div class="flex justify-center mb-4">
                        @if($component->image_url)
                            @php
                                $imagePath = 'products/' . basename($component->image_url);
                                $url = asset('storage/' . $imagePath);
                            @endphp
                            
                            <img 
                                src="{{ $url }}" 
                                alt="{{ $component->name }}" 
                                class="max-w-full h-auto max-h-64 object-contain rounded shadow"
                                onerror="this.onerror=null; this.src='{{ asset('images/defaulte_image.jpg') }}'"
                            >
                        @else
                      
                            <img 
                            src="{{ asset('images/defaulte_image.jpg') }}" 
                            alt="Default product image"
                            style="width: 200px; height: 200px;">
                        
                        @endif
                    </div>
                    <h2 class="text-lg font-semibold">{{ $component->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $component->brand }}</p>
                    <p class="font-bold text-green-600 mt-2">{{ number_format($component->price, 2) }} </p>
                    <a href="{{ route('components.show', $component->id) }}" class="inline-block mt-2 text-blue-500 hover:underline">
                        Подробнее
                    </a>
                    @if (auth()->check() && auth()->user()->admin == 1)
                    <button type="submit" class="mt-2 text-red-600 hover:underline">Удалить</button>
                    @endif
                    
                </div>
            </form>
            @empty
                <p>Комплектующие не найдены.</p>
            @endforelse
        </div>

        {{--Пагинация --}}
        <div class="mt-6">
            {{ $components->withQueryString()->links() }}
        </div>
    </div>
    <script>
         // Получаем все элементы с классом "accordion"
    var acc = document.getElementsByClassName("accordion");
    
    // Для каждого аккордеона добавляем обработчик событий
    for (var i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            // Переключаем видимость панели
            this.classList.toggle("active");
            
            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }
        </script>
       
        
</body>
</html>
