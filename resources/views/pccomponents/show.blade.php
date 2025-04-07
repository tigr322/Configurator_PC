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
                    <div class="w-full md:w-1/3 mb-6">
                        <img src="{{ $component->image_url }}" alt="{{ $component->name }}" class="w-full rounded shadow">
                    </div>
                @endif

                {{-- Информация --}}
                <div class="w-full max-w-md space-y-4"> <!-- Фиксированная ширина и отступы -->
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
    
            <div class="flex flex-col md:flex-row gap-6 w-full max-w-4xl">
                {{-- Картинка --}}
                @if($component->image_url)
                    <div class="w-full md:w-1/3">
                        <img src="{{ $component->image_url }}" alt="{{ $component->name }}" class="w-full rounded shadow">
                    </div>
                @endif
    
                {{-- Информация --}}
                <div class="w-full md:w-2/3 space-y-2">
                    <p><strong>Категория:</strong> {{ $component->category->name }}</p>
                    <p><strong>Бренд:</strong> {{ $component->brand ?? 'Не указан' }}</p>
                    <p><strong>Цена:</strong> {{ number_format($component->price, 2, ',', ' ') }} руб</p>
    
                    @if($component->shop_url)
                        <a href="{{ $component->shop_url }}" target="_blank" class="text-blue-500 underline">
                            Перейти в магазин
                        </a>
                    @endif
    
                    {{-- Совместимость --}}
                    <div>
                        <h2 class="text-lg font-semibold mt-4 mb-2">Совместимость</h2>
                        <div class="bg-gray-100 p-2 rounded text-sm text-black">
                            @php
                                $compatibility = json_decode($component->compatibility_data, true);
                            @endphp
                    
                            @if($compatibility)
                                <ul style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" class="list-disc pl-5">
                                    @foreach($compatibility as $key => $value)
                                        <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p>Нет данных о совместимости.</p>
                            @endif
                        </div>
                    </div>
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