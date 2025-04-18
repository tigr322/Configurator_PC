<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ $component->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @include('layouts.navigation')

    <div class="container mx-auto px-4 py-8">
        @if (auth()->check() && auth()->user()->admin == 1)
        <form action="{{ route('components.update', $component->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="flex flex-col items-center"> <!-- Центрируем содержимое -->
                {{-- Картинка --}}
                @if($component->image_url)
                <div class="flex justify-center mb-4">
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
                    </div>
                @endif

                {{-- Информация --}}
                <div class="w-full max-w-md space-y-4"> <!-- Фиксированная ширина и отступы -->
                    <div class="mb-4">
                        <label class="block font-semibold">Категория:</label>
                        <select name="category_id" id="category_id" required
                            class="w-full px-3 py-2 border rounded bg-white text-black">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $component->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold">Название:</label>
                        <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" 
                               type="text" name="name" value="{{ $component->name }}" class="w-full border p-2 rounded">
                    </div>

                    <div>
                        <label class="block font-semibold">Бренд:</label>
                        <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" 
                               type="text" name="brand" value="{{ $component->brand }}" class="w-full border p-2 rounded">
                    </div>

                    <div>
                        <label class="block font-semibold">Цена (руб):</label>
                        <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" 
                               type="number" step="0.01" name="price" value="{{ $component->price }}" class="w-full border p-2 rounded">
                    </div>

                    <div>
                        <label class="block font-semibold">Ссылка на магазин:</label>
                        <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" 
                               type="url" name="shop_url" value="{{ $component->shop_url }}" class="w-full border p-2 rounded">
                    </div>
                    <div>
                        <label class="block font-semibold">Характеристики</label>
                        <textarea 
                          style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; height: 200px; font-size: 0.875rem; overflow-x: auto; color: black;" 
                          name="characteristics"
                          class="w-full border p-2 rounded"
                        >{{ str_replace(';', ";\n", $component->characteristics) }}</textarea>
                      </div>
                    <div>
                        <label class="block font-semibold">Совместимость (JSON):</label>
                        <textarea style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" 
                                  name="compatibility_data" class="w-full border p-2 rounded h-32">{{ $component->compatibility_data }}</textarea>
                        <p class="text-sm text-gray-500">Например: {"socket": "AM4", "form_factor": "ATX"}</p>
                    </div>

                    <div class="text-center"> <!-- Центрируем кнопку -->
                        <button type="submit" class="inline-block px-5 py-1.5 border border-transparent hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                            Сохранить изменения
                        </button>
                    </div>
                </div>
            </div>
        </form>
        @else 
        <div class="flex flex-col items-center"> <!-- Центрируем содержимое -->
            <h1 class="text-2xl font-bold mb-4">{{ $component->name }}</h1>
             @if($component->image_url)
                <div class="flex justify-center mb-4">
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
                    </div>
                @endif
            <div class="flex flex-col md:flex-row gap-6 w-full max-w-4xl">
                {{-- Картинка --}}
               
    
                {{-- Информация --}}
                <div class="w-full md:w-2/3 space-y-2">
                    <p><strong>Категория:</strong> {{ $component->category->name }}</p>
                    <p><strong>Бренд:</strong> {{ $component->brand ?? 'Не указан' }}</p>
                    <p><strong>Цена:</strong> {{ number_format($component->price, 2, ',', ' ') }} руб</p>
                    <p><strong>Характеристики: </strong> {{ str_replace(';', ";\n", $component->characteristics) }}</p>
                    @if($component->shop_url)
                        <a href="{{ $component->shop_url }}" target="_blank" class="text-blue-500 underline">
                            Перейти в магазин
                        </a>
                    @endif
    
                    {{-- Совместимость --}}
                    
                </div>
            </div>
        @endif
        
        {{-- Источники цен --}}
        @if($component->parsedData->count())
            <div class="mt-8 w-full max-w-4xl mx-auto">
                <h2 class="text-lg font-semibold mb-2">Цены в магазинах</h2>
                <ul class="list-disc ml-5">
                    @foreach($component->parsedData as $parsed)
                        <li>
                            <strong>{{ $parsed->source }}:</strong>
                            {{ number_format($parsed->price, 2, ',', ' ') }} ₽
                            {{ $parsed->availability ? '(В наличии)' : '(Нет в наличии)' }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</body>
</html>