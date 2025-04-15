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
   
    <div class="relative h-screen">
       
        {{-- Форма фильтрации --}}
       
</div>
    <div class="container mx-auto px-4 py-3">
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
        <div class="container mx-auto px-4 py-3 flex flex-wrap">
            <div 
            class="Filters shadow-md"
            style="
                @if (!auth()->check() || auth()->user()->admin != 1)
                    width: 100%;
                @else
                    width: 30%;
                @endif
            "
        >
            <h2 class="text-xl font-bold mb-6" style="font-size: 20px;" >Фильтрация</h2>
        <form method="GET" action="{{ route('catalog') }}" class="mb-3 flex flex-col gap-2">
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
    </div>
        
@if (auth()->check() && auth()->user()->admin == 1)
    
<div style="width: 50%; margin: 0 auto;">
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
    <button class="accordion w-full mt-4 bg-green-600 text-white py-1.5 text-sm rounded hover:bg-green-700 transition">
        ✏ Правила совместимости комплектующих
    </button>
    <div class="panel hidden mt-2">
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
                            <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" type="text" name="rules[{{ $index }}][condition]" value="{{ json_encode($rule->condition) }}" class="w-full border px-1">
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


@endif
</div>
        {{-- Список компонентов --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-6">Каталог комплектующих</h1>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse ($components as $component)
                <form method="POST" action="{{ route('delete', $component->id) }}" class="h-full">
                    @csrf
                    @method('DELETE')
                    <!-- Добавлен класс h-full к родительскому div -->
                    <div class="w-[280px] h-[420px] border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex flex-col" style = "height: 430px;">
                        <!-- Контейнер изображения с фиксированной высотой -->
                        <div class="h-[200px] flex items-center justify-center mb-3">
                            @if($component->image_url)
                                @php
                                    $imagePath = 'products/' . basename($component->image_url);
                                    $url = asset('storage/' . $imagePath);
                                @endphp
                                
                                <img 
                                    src="{{ $url }}" 
                                    alt="{{ $component->name }}" 
                                    class="max-w-full max-h-full object-contain"
                                    onerror="this.onerror=null; this.src='{{ asset('images/defaulte_image.jpg') }}'"
                                >
                            @else
                                <img 
                                    src="{{ asset('images/defaulte_image.jpg') }}" 
                                    alt="Default product image"
                                    class="max-w-full max-h-full object-contain">
                            @endif
                        </div>
                        
                        <div class="mt-auto">
                            <h2 class="text-md font-medium line-clamp-2 h-12 mb-1">{{ $component->name }}</h2>
                            <p class="text-xs text-gray-500 mb-2">{{ $component->brand }}</p>
                            
                            <div class="flex items-center justify-between mt-3">
                                <p class="font-bold text-green-600 text-lg">{{ number_format($component->price, 0, '', ' ') }} ₽</p>
                                <a href="{{ route('components.show', $component->id) }}" class="text-blue-500 hover:text-blue-700 text-sm">
                                    Подробнее
                                </a>
                            </div>
                            
                            @if (auth()->check() && auth()->user()->admin == 1)
                            <button type="submit" class="mt-2 text-red-600 hover:text-red-800 text-sm">Удалить</button>
                            @endif
                        </div>
                    </div>
                </form>
                @empty
                    <p class="col-span-full text-center py-10 text-gray-500">Комплектующие не найдены.</p>
                @endforelse
            </div>
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
    
</body>
</html>
