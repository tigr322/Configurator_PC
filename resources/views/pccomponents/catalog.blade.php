<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог комплектующих</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

</head>

<body>

    @include('layouts.navigation')
   
    
    <div class="container mx-auto">
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
        
        {{-- Список компонентов --}}
        <div class="flex flex-col lg:flex-row">
            <div class="lg:w-1/4">
                <div 
                class=""
                style="padding-top:60px; padding-right: 20px;
                "
            >
            <div class="lg:hidden mb-4">
                <button onclick="toggleSidebar()" class="flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    Фильтры
                </button>
            </div>
            <div  id="filter"  class="hidden lg:block lg:bg-transparent p-4 lg:p-0 lg:pt-15 lg:pr-5">
                <h2 class="text-xl font-bold mb-6" style="font-size: 20px;" >Фильтрация</h2>
            <form id="filter-form"  method="GET"  action="{{ route('catalog') }}" class="mb-3 flex flex-col gap-2">
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
                <input type="hidden" id="socket-filter" name="socket" value="{{ request('socket') }}">
                <input type="hidden" id="manufacturer-filter" name="manufacturer" value="{{ request('manufacturer') }}">
                <input type="hidden" id="memory-type-filter" name="memory_type" value="{{ request('memory_type') }}">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded col-span-1 md:col-span-2">Применить</button>
            </form>
        
            <div class="mb-6 space-y-4">
                <!-- Группа фильтров по сокетам процессоров -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-2">Сокеты процессоров</h3>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="filterBySocket('AM4')" 
                                class="px-3 py-1 hover:bg-blue-100 rounded-full text-sm transition border border-blue-100 
                                       {{ request('socket') == 'AM4' ? 'bg-blue-100 border-blue-300' : '' }}">
                            AM4
                        </button>
                        <button type="button" onclick="filterBySocket('AM5')" 
                                class="px-3 py-1 hover:bg-blue-100 rounded-full text-sm transition border border-blue-100 
                                       {{ request('socket') == 'AM5' ? 'bg-blue-100 border-blue-300' : '' }}">
                            AM5
                        </button>
                        <button type="button" onclick="filterBySocket('LGA1700')" 
                                class="px-3 py-1 hover:bg-blue-100 rounded-full text-sm transition border border-blue-100 
                                       {{ request('socket') == 'LGA1700' ? 'bg-blue-100 border-blue-300' : '' }}">
                            LGA1700
                        </button>
                        <button type="button" onclick="filterBySocket('LGA1200')" 
                                class="px-3 py-1 hover:bg-blue-100 rounded-full text-sm transition border border-blue-100 
                                       {{ request('socket') == 'LGA1200' ? 'bg-blue-100 border-blue-300' : '' }}">
                            LGA1200
                        </button>
                    </div>
                </div>
            
                <!-- Группа фильтров по производителям видеокарт -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-2">Производители видеокарт</h3>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="filterByManufacturer('Gigabyte')" 
                                class="px-3 py-1 hover:bg-blue-100 rounded-full text-sm transition border border-blue-100 
                                       {{ request('manufacturer') == 'Gigabyte' ? 'bg-blue-100 border-blue-300' : '' }}">
                                       Gigabyte
                        </button>
                        <button type="button" onclick="filterByManufacturer('ASUS')" 
                                class="px-3 py-1 hover:bg-blue-100 rounded-full text-sm transition border border-blue-100 
                                       {{ request('manufacturer') == 'ASUS' ? 'bg-blue-100 border-blue-300' : '' }}">
                                       ASUS
                        </button>
                        <button type="button" onclick="filterByManufacturer('Palit')" 
                                class="px-3 py-1 hover:bg-blue-100 rounded-full text-sm transition border border-blue-100 
                                       {{ request('manufacturer') == 'Palit' ? 'bg-blue-100 border-blue-300' : '' }}">
                            Palit
                        </button>
                    </div>
                </div>
            
                <!-- Группа фильтров по типу памяти -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-2">Тип памяти</h3>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="filterByMemoryType('DDR4')" 
                                class="px-3 py-1 hover:bg-blue-100 rounded-full text-sm transition border border-blue-100 
                                       {{ request('memory_type') == 'DDR4' ? 'bg-blue-100 border-blue-300' : '' }}">
                            DDR4
                        </button>
                        <button type="button" onclick="filterByMemoryType('DDR5')" 
                                class="px-3 py-1 hover:bg-blue-100 rounded-full text-sm transition border border-blue-100 
                                       {{ request('memory_type') == 'DDR5' ? 'bg-blue-100 border-blue-300' : '' }}">
                            DDR5
                        </button>
                    </div>
                </div>
            
                <!-- Кнопка сброса (показывается только если есть активные фильтры) -->
                @if(request('socket') || request('manufacturer') || request('memory_type'))
                <div class="pt-2">
                    <button type="button" onclick="clearAllFilters()" 
                            class="px-3 py-1 hover:bg-blue-100 rounded-full text-sm transition border border-blue-200 text-blue-600">
                        Сбросить все фильтры
                    </button>
                </div>
                @endif
            </div>
     
    @if (auth()->check() && auth()->user()->admin == 1)
        
    <div style="padding-right: 20px;">
        <h2 class=" font-bold mb-6">🔧 Админ-панель</h2>
    
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
            <form method="POST" action="{{ route('admin.parse') }}" id="parser-form">
                @csrf
                <div class="mb-3">
                    <label for="markets-select" class="block mb-1 text-sm">Выберите магазин:</label>
                    <select id="markets-select" name="market_id" required 
                            class="w-full border px-1 py-2 bg-gray-100 text-sm text-black rounded">
                        <option value="">— Выберите —</option>
                        @foreach($markets as $market)
                            <option value="{{ $market->id }}">{{ $market->name }}</option>
                        @endforeach
                    </select>
                </div>
            
                <div id="urls-container" class="mb-3 hidden">
                    <label for="category_id" class="block mb-1">Доступные категории:</label>
                    <select id="categories_id" name="category_id" required
                            class="w-full px-2 py-1 border rounded text-black bg-white">
                        <!-- Опции будут загружены через AJAX -->
                    </select>
                </div>
            
                <button type="submit" id="submit-btn" disabled
                        class="w-full bg-green-600 text-white py-1 rounded hover:bg-green-700 transition opacity-50">
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
                    <label for="market_id" class="block   mb-1">Магазин</label>
                    <select id="market_id" name="market_id" required
                        class="w-full px-2 py-1 border rounded text-black   bg-white">
                        @foreach ($markets as $market)
                            <option value="{{ $market->id }}">{{ $market->name }}</option>
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
                    <label class="block font-semibold   mb-1">Характеристика</label>
                    <textarea name="charastiristics"
                              class="w-full border p-2 rounded h-28   bg-gray-100 text-black resize-none"
                              placeholder=''></textarea>
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
        
        <button id="open-modal" class="w-full mt-4 bg-green-600 text-white py-1.5 text-sm rounded hover:bg-green-700 transition">
            ✏ Правила совместимости комплектующих
        </button>
        
        <!-- Затемнение фона + Модальное окно -->
        <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div style="background-color: black;" class="text-white p-6 rounded-lg shadow-lg w-full max-w-6xl overflow-auto max-h-[90vh] relative">

        
                <!-- Кнопка закрытия -->
                <button id="close-modal" class="absolute top-3 right-4 text-gray-600 hover:text-black text-2xl">&times;</button>
        
                <!-- Форма -->
                <form id="compatibility-form" action="{{ route('save.compatibility.rules') }}" method="POST">
                    @csrf
                    <table class="w-full border mt-2 text-sm" id="compatibility-table">
                        <thead>
                            <tr>
                                <th class="border px-2 py-1">ID</th>
                                <th class="border px-2 py-1">Категория 1</th>
                                <th class="border px-2 py-1">Категория 2</th>
                                <th class="border px-2 py-1">Правила (JSON)</th>
                                <th class="border px-2 py-1">Удалить</th>
                            </tr>
                        </thead>
                        <tbody id="compatibility-rows">
                            @foreach ($rules as $index => $rule)
                            <tr>
                                <td class="border px-2 py-1">
                                    {{ $rule->id }}
                                    <input type="hidden" name="rules[{{ $index }}][id]" value="{{ $rule->id }}">
                                </td>
                                <td class="border px-2 py-1">
                                    <select name="rules[{{ $index }}][category1_id]" class="w-full border px-1 py-2 bg-gray-100 text-sm text-black rounded">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $category->id == $rule->category1_id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="border px-2 py-1">
                                    <select name="rules[{{ $index }}][category2_id]" class="w-full border px-1 py-2 bg-gray-100 text-sm text-black rounded">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $category->id == $rule->category2_id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="border px-2 py-1">
                                    <textarea name="rules[{{ $index }}][condition]" 
                                              class="w-full border px-1 py-2 bg-gray-100 text-sm text-black rounded" 
                                              style="min-height: 40px;">
                                        {{ json_encode($rule->condition) }}
                                    </textarea>
                                </td>
                                <td class="border px-2 py-1 text-center">
                                    <button type="button" class="remove-row text-red-600" data-id="{{ $rule->id }}">✖</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
        
                    <button type="button" id="add-rule" class="bg-blue-600 text-white px-3 py-1 mt-2 rounded hover:bg-blue-700 transition">
                        ➕ Добавить правило
                    </button>
        
                    <input type="hidden" id="deleted-rules" name="deleted_rules" value="[]">
        
                    <button type="submit" class="w-full bg-green-600 text-white py-1 rounded hover:bg-green-700 transition mt-2">
                        💾 Сохранить изменения
                    </button>
                </form>
            </div>
        </div>
        <button class="accordion w-full mt-4 bg-green-600 text-white py-1.5 text-sm rounded hover:bg-green-700 transition">
            ✏ Добавить URL магазина для парсинга
        </button>
        <div class="panel hidden mt-2">
            <form id="markets-urls-form" action="{{ route('markets_urls.save') }}" method="POST">
                @csrf
        
                <div class="mb-3">
                    <label for="market-select" class="block mb-1 text-sm">Выберите магазин:</label>
                    <select id="market-select" name="market_id" class="w-full border px-1 py-2 bg-gray-100 text-sm text-black rounded">
                        <option value="">— Выберите —</option>
                        @foreach($markets as $market)
                            <option value="{{ $market->id }}">{{ $market->name }}</option>
                        @endforeach
                    </select>
                </div>
        
                <div id="categories-url-table-wrapper" class="hidden">
                    <table id="categories-url-table" class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="border px-2 py-1">Категория</th>
                                <th class="border px-2 py-1">URL</th>
                                <th class="border px-2 py-1">Удалить</th>
                            </tr>
                        </thead>
                        <tbody id="categories-url-body">
                            @foreach($marketsUrls as $index => $marketUrl)
                                <tr>
                                    <td class="border px-2 py-1">
                                        <select name="urls[{{ $index }}][category_id]" class="category-select w-full border px-1 py-2 bg-gray-100 text-sm text-black rounded">
                                            <option value="">— Категория —</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $marketUrl->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border px-2 py-1">
                                        <input type="text" name="urls[{{ $index }}][url]" class="w-full border px-2 py-1 rounded bg-gray-100 text-black" value="{{ $marketUrl->url }}">
                                    </td>
                                    <td class="border px-2 py-1 text-center">
                                        <button type="button" class="remove-row text-red-600" data-id="{{ $marketUrl->id }}">✖</button>
    
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
        
                    <button type="button" id="add-row" class="bg-blue-600 text-white px-3 py-1 mt-2 rounded hover:bg-blue-700 transition">
                        ➕ Добавить строку
                    </button>
                </div>
        
                <button type="submit" class="w-full bg-green-600 text-white py-1 rounded hover:bg-green-700 transition mt-3">
                    💾 Сохранить
                </button>
            </form>
        </div>
        <div class="mb-4  py-3">
        <button class="accordion w-full bg-green-600 text-white py-1.5 text-sm rounded hover:bg-green-700 transition mb-6">
            ➕ Добавить магазин
        </button>
        <div class="panel hidden mt-2">
            <form method="POST" action="{{ route('admin.addMarket') }}">
                @csrf
                <div class="mb-2">
                    <label for="market_name" class="block   mb-1">Новая магазин</label>
                    <input type="text" name="market_name" id="market_name" required
                        class="w-full px-2 py-1 border rounded text-black   bg-white">
                </div>
                <button type="submit"
                    class="w-full bg-green-600 text-white py-1 rounded   hover:bg-green-700 transition">
                    Добавить
                </button>
            </form>
            </div>
            </div>
        </div>
            @endif
        </div>
    </div>
    </div>      
            <div class="lg:w-3/4">
            <div class="flex justify-between items-center">
                
                <!-- Переключатель вида -->
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Вид:</span>
                    <button id="grid-view" class="p-2 rounded-md bg-blue-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    <button id="list-view" class="p-2 rounded-md hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            <h1 class="text-3xl font-bold">Каталог комплектующих</h1>
                
            <!-- Версия плиткой (по умолчанию) -->
            
                <div class="mx-auto max-w-2xl lg:max-w-7xl lg:px-8">
                
                  <div id="grid-version" class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
                    @forelse ($components as $component)
                    <form method="POST" action="{{ route('delete', $component->id) }}" class="group">
                      @csrf
                      @method('DELETE')
                      <div class="border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
                        <!-- Изображение -->
                        <div class="aspect-square w-full rounded-lg bg-gray-100 flex items-center justify-center mb-4 overflow-hidden">
                          @if($component->image_url)
                            @php
                              $imagePath = 'products/' . basename($component->image_url);
                              $url = asset('storage/' . $imagePath);
                            @endphp
                            <img src="{{ $url }}" 
                                 alt="{{ $component->name }}" 
                                 class="w-full h-full object-contain group-hover:opacity-75"
                                 onerror="this.onerror=null; this.src='{{ asset('images/defaulte_image.jpg') }}'">
                          @else
                            <img src="{{ asset('images/defaulte_image.jpg') }}" 
                                 alt="Default product image" 
                                 class="w-full h-full object-contain group-hover:opacity-75">
                          @endif
                        </div>
                        
                        <!-- Информация о товаре -->
                        <div class="flex-grow">
                          <h3 class="text-sm font-medium line-clamp-2 mb-1">{{ $component->name }}</h3>
                          <p class="text-xs text-gray-500">{{ $component->brand }}</p>
                        </div>
                        
                        <!-- Цена и кнопки -->
                        <div class="mt-4">
                          <div class="flex items-center justify-between border-t pt-3">
                            <p class="text-lg font-medium text-green-600">{{ number_format($component->price, 0, '', ' ') }} ₽</p>
                            <a href="{{ route('components.show', $component->id) }}" 
                               class="text-sm font-medium text-blue-600 hover:text-blue-500">
                              Подробнее
                            </a>
                          </div>
                          
                          @if (auth()->check() && auth()->user()->admin == 1)
                          <button type="button" 
                          onclick="deleteComponent(event, {{ $component->id }})" 
                          class="text-red-600 hover:text-red-800 text-sm font-medium">
                      Удалить
                  </button>
                          @endif
                        </div>
                      </div>
                    </form>
                    @empty
                      <p class="col-span-full text-center py-10 text-gray-500">Комплектующие не найдены.</p>
                    @endforelse
                  </div>
                </div>
             
              <div id="list-version" class="hidden space-y-4">
                @forelse ($components as $component)
                <form method="POST" action="{{ route('delete', $component->id) }}" class="w-full group">

                    @csrf
                    @method('DELETE')
                    <div class="border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex items-start gap-4">
                        <!-- Контейнер изображения -->
                        <div class="flex-shrink-0 w-48 h-48 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                            @if($component->image_url)
                                @php
                                    $imagePath = 'products/' . basename($component->image_url);
                                    $url = asset('storage/' . $imagePath);
                                @endphp
                                <img src="{{ $url }}" 
                                     alt="{{ $component->name }}" 
                                    class="w-full h-full object-contain group-hover:opacity-75"
                                     onerror="this.onerror=null; this.src='{{ asset('images/defaulte_image.jpg') }}'">
                            @else
                                <img src="{{ asset('images/defaulte_image.jpg') }}" 
                                     alt="Default product image" 
                                     class="w-full h-full object-contain group-hover:opacity-75">
                            @endif
                        </div>
                        
                        <!-- Основной контент -->
                        <div class="flex-1 min-w-0">
                            <!-- Заголовок и цена -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                                <h2 class="text-lg font-medium truncate">{{ $component->name }}</h2>
                                <p class="text-lg font-semibold text-green-600 whitespace-nowrap">
                                    {{ number_format($component->price, 0, '', ' ') }} ₽
                                </p>
                            </div>
                            
                            <!-- Бренд -->
                            <p class="text-sm text-gray-500 mb-3">{{ $component->brand }}</p>
                            
                            <!-- Характеристики -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 mb-4">
                                @foreach(explode(',', $component->characteristics) as $char)
                                <span class="text-sm px-2 py-1 rounded">
                                    {{ trim($char) }}
                                </span>
                                @endforeach
                            </div>
                            @if($component->shop_url)
                            <a href="{{ $component->shop_url }}" target="_blank" class="text-blue-500 underline">
                                Перейти в магазин
                            </a>
                            @endif
                            <!-- Кнопки действий -->
                            <div class="flex justify-between items-center pt-3 border-t">
                                <div class="flex space-x-4">
                                    
                                    @if (auth()->check() && auth()->user()->admin == 1)
                                    <a href="{{ route('components.show', $component->id) }}" 
                                       class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                        Редактировать
                                    </a>
                                    @endif
                                </div>
                                
                                @if (auth()->check() && auth()->user()->admin == 1)
                                <button type="button" 
                                onclick="deleteComponent(event, {{ $component->id }})" 
                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                            Удалить
                        </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
                @empty
                <div class="text-center py-10">
                    <p class="text-gray-500">Комплектующие не найдены.</p>
                </div>
                @endforelse
            </div>
       
        <div class="mt-6">
            {{ $components->withQueryString()->links() }}
        </div>
    </div>
</div>
</div>
    <script>
       
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
       
       <script>
        document.addEventListener("DOMContentLoaded", () => {
            let rowIndex = {{ count($rules) }};
            const tableBody = document.getElementById("compatibility-rows");
    
            document.getElementById("add-rule").addEventListener("click", () => {
                const row = document.createElement("tr");
                row.innerHTML = `
    <td class="border px-2 py-1">
        <input type="hidden" name="rules[${rowIndex}][id]" value="">
        Новый
    </td>
    <td class="border px-2 py-1">
        <select name="rules[${rowIndex}][category1_id]" class="border p-2 rounded w-full bg-gray-100 text-sm text-black">
            <option value="">Выбери категорию</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </td>
    <td class="border px-2 py-1">
        <select name="rules[${rowIndex}][category2_id]" class="border p-2 rounded w-full bg-gray-100 text-sm text-black">
            <option value="">Выбери категорию</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </td>
    <td class="border px-2 py-1">
        <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" type="text" name="rules[${rowIndex}][condition]" class="w-full border px-1" placeholder='{"socket": "=="}'>
    </td>
    <td class="border px-2 py-1 text-center">
        <button type="button" class="remove-row text-red-600">✖</button>
    </td>
`;

                tableBody.appendChild(row);
                rowIndex++;
            });
    
            // удаление строки
            tableBody.addEventListener("click", (e) => {
                if (e.target.classList.contains("remove-row")) {
                    e.target.closest("tr").remove();
                }
            });
        });
    </script>  
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Обработчик для удаления строки
            const tableBody = document.getElementById("compatibility-rows");
            const deletedRulesInput = document.getElementById("deleted-rules");
    
            tableBody.addEventListener("click", (e) => {
                if (e.target.classList.contains("remove-row")) {
                    const row = e.target.closest("tr");  // Находим строку, которая была нажата
                    const ruleId = e.target.getAttribute("data-id");

                    if (ruleId) {
                        let deletedRules = deletedRulesInput.value ? JSON.parse(deletedRulesInput.value) : [];
                        deletedRules.push(ruleId);
                        deletedRulesInput.value = JSON.stringify(deletedRules);
                    }
                   
    
                    row.remove();  // Удаляем строку из таблицы
                }
            });
        });
    </script>
   <script>
    document.addEventListener('DOMContentLoaded', function () {
        const marketSelect = document.getElementById('market-select');
    const tableWrapper = document.getElementById('categories-url-table-wrapper');
    const tableBody = document.getElementById('categories-url-body');
    const addRowButton = document.getElementById('add-row');
    let categories = @json($categories); // заранее передаём категории
    let rowIndex = {{ count($marketsUrls) }};

    marketSelect.addEventListener('change', function () {
        const marketId = this.value;

        if (marketId) {
            fetch(`/markets-urls/${marketId}`)
                .then(response => response.json())
                .then(data => {
                    const urls = data.urls;
                    categories = data.categories;
                    tableBody.innerHTML = '';

                    urls.forEach((urlItem, index) => {
                        const row = createRow(index, urlItem.category_id, urlItem.url, urlItem.id);
                        tableBody.appendChild(row);
                    });

                    tableWrapper.classList.remove('hidden');
                    rowIndex = urls.length;
                });
        } else {
            tableWrapper.classList.add('hidden');
        }
    });

    addRowButton.addEventListener('click', function () {
        const row = createRow(rowIndex, '', '');
        tableBody.appendChild(row);
        rowIndex++;
    });

    // Функция генерации строки
    function createRow(index, selectedCategoryId = '', url = '', id = null) {
        const row = document.createElement('tr');

        const options = categories.map(category => {
            const selected = category.id == selectedCategoryId ? 'selected' : '';
            return `<option value="${category.id}" ${selected}>${category.name}</option>`;
        }).join('');

        row.innerHTML = `
            <td class="border px-2 py-1">
                <select name="urls[${index}][category_id]" class="category-select w-full border px-1 py-2 bg-gray-100 text-sm text-black rounded">
                    <option value="">— Категория —</option>
                    ${options}
                </select>
            </td>
            <td class="border px-2 py-1">
                <input type="text" name="urls[${index}][url]" class="w-full border px-2 py-1 rounded bg-gray-100 text-black" value="${url}">
            </td>
            <td class="border px-2 py-1 text-center">
                <button type="button" class="remove-row text-red-600" data-id="${id ?? ''}">✖</button>
            </td>
        `;

        return row;
    }

    // Слушатель для удаления строки
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
        tableBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row')) {
                const row = e.target.closest('tr');
                const id = e.target.getAttribute('data-id');
    
                if (id) {
                    if (confirm('Вы уверены, что хотите удалить эту ссылку?')) {
                        fetch(`/markets-urls/delete/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        })
                        .then(response => {
                            if (response.ok) {
                                row.remove();
                            } else {
                                alert('Ошибка при удалении. Попробуйте снова.');
                            }
                        })
                        .catch(() => alert('Ошибка при удалении. Попробуйте снова.'));
                    }
                } else {
                    row.remove();
                }
            }
        });
    });
    </script>
    
    <script>
        document.getElementById('markets-select').addEventListener('change', function() {
            const marketId = this.value;
            const urlsContainer = document.getElementById('urls-container');
            const submitBtn = document.getElementById('submit-btn');
            
            if (!marketId) {
                urlsContainer.classList.add('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50');
                return;
            }
        
            // AJAX-запрос для получения URL
            fetch(`/admin/get-urls?market_id=${marketId}`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('categories_id');
                    select.innerHTML = '';
                    
                    if (Object.keys(data).length === 0) {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Нет доступных категорий';
                        select.appendChild(option);
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50');
                    } else {
                        for (const [categoryId, urls] of Object.entries(data)) {
                        const categoryName = urls[0].category.name;
                        urls.forEach(url => {
                            const option = document.createElement('option');
                            option.value = categoryId;
                            option.textContent = `${categoryName} - ${url.url}`;
                            option.dataset.categoryId = categoryId; // Дополнительная проверка
                            select.appendChild(option);
                        });
                    }
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50');
                    }
                    
                    urlsContainer.classList.remove('hidden');
                });
        });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const gridViewBtn = document.getElementById('grid-view');
                const listViewBtn = document.getElementById('list-view');
                const gridVersion = document.getElementById('grid-version');
                const listVersion = document.getElementById('list-version');
                
                // Проверяем сохраненное значение в localStorage
                const savedView = localStorage.getItem('preferredView') || 'grid';
                
                // Устанавливаем начальное состояние
                if (savedView === 'grid') {
                    gridVersion.classList.remove('hidden');
                    listVersion.classList.add('hidden');
                    gridViewBtn.classList.add('bg-blue-100', 'text-blue-600');
                    listViewBtn.classList.remove('bg-blue-100', 'text-blue-600');
                } else {
                    gridVersion.classList.add('hidden');
                    listVersion.classList.remove('hidden');
                    gridViewBtn.classList.remove('bg-blue-100', 'text-blue-600');
                    listViewBtn.classList.add('bg-blue-100', 'text-blue-600');
                }
                
                // Обработчики кликовd
                gridViewBtn.addEventListener('click', function() {
                    gridVersion.classList.remove('hidden');
                    listVersion.classList.add('hidden');
                    gridViewBtn.classList.add('bg-blue-100', 'text-blue-600');
                    listViewBtn.classList.remove('bg-blue-100', 'text-blue-600');
                    localStorage.setItem('preferredView', 'grid');
                });
                
                listViewBtn.addEventListener('click', function() {
                    gridVersion.classList.add('hidden');
                    listVersion.classList.remove('hidden');
                    gridViewBtn.classList.remove('bg-blue-100', 'text-blue-600');
                    listViewBtn.classList.add('bg-blue-100', 'text-blue-600');
                    localStorage.setItem('preferredView', 'list');
                });
            });
            </script>
            <script>
                function deleteComponent(event, id) {
                        event.preventDefault(); // Важно: предотвращаем действие по умолчанию
                        
                        if (!confirm('Вы уверены, что хотите удалить этот компонент?')) return;

                        const componentElement = event.target.closest('form'); // Находим родительскую форму

                        fetch(`/delete/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Ошибка сети');
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Плавное исчезновение элемента
                                componentElement.style.transition = 'opacity 0.3s, transform 0.3s';
                                componentElement.style.opacity = '0';
                                componentElement.style.transform = 'scale(0.9)';
                                
                                // Удаление после анимации
                                setTimeout(() => {
                                    componentElement.remove();
                                    
                                    // Обновляем счетчик или другие элементы при необходимости
                                    updateComponentsCounter();
                                }, 300);
                                
                                // Показываем уведомление
                                showFlashMessage(data.message, 'success');
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка:', error);
                            showFlashMessage('Ошибка при удалении', 'error');
                        });
                    }

                    // Функция для показа уведомлений
                    function showFlashMessage(message, type) {
                        const flash = document.createElement('div');
                        flash.className = `fixed top-4 right-4 px-4 py-2 rounded-md text-white ${
                            type === 'success' ? 'bg-green-500' : 'bg-red-500'
                        } z-50`;
                        flash.textContent = message;
                        document.body.appendChild(flash);
                        
                        setTimeout(() => {
                            flash.style.transition = 'opacity 0.5s';
                            flash.style.opacity = '0';
                            setTimeout(() => flash.remove(), 500);
                        }, 3000);
                    }

                    // Если нужно обновить счетчик компонентов
                    function updateComponentsCounter() {
                        const counter = document.getElementById('components-counter');
                        if (counter) {
                            counter.textContent = parseInt(counter.textContent) - 1;
                        }
                    }
                </script>

<script>
    // Фильтрация по сокету
    function filterBySocket(socket) {
        document.getElementById('socket-filter').value = socket;
        document.getElementById('filter-form').submit();
    }
    
    // Фильтрация по производителю
    function filterByManufacturer(brand) {
        document.getElementById('manufacturer-filter').value = brand;
        document.getElementById('filter-form').submit();
    }
    
    // Фильтрация по типу памяти
    function filterByMemoryType(memoryType) {
        document.getElementById('memory-type-filter').value = memoryType;
        document.getElementById('filter-form').submit();
    }
    
    // Сброс всех фильтров
    function clearAllFilters() {
        document.getElementById('socket-filter').value = '';
        document.getElementById('manufacturer-filter').value = '';
        document.getElementById('memory-type-filter').value = '';
        document.getElementById('filter-form').submit();
    }
    </script>


<!-- JS для открытия/закрытия модального окна -->
<!-- JS для открытия/закрытия модального окна -->
<script>
    const openModal = document.getElementById('open-modal');
    const closeModal = document.getElementById('close-modal');
    const modal = document.getElementById('modal');

    openModal.addEventListener('click', () => {
        modal.classList.remove('hidden');
    });

    closeModal.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // Закрытие по клику на затемнение
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
</script>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('filter');
        sidebar.classList.toggle('hidden');
    }
</script>
</body>
</html>
