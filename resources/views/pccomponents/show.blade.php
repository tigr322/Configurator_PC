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
        <h1 class="text-2xl font-bold mb-4">{{ $component->name }}</h1>

        <div class="flex flex-col md:flex-row gap-6">
            {{-- Картинка --}}
            @if($component->image_url)
                <div class="w-full md:w-1/3">
                    <img src="{{ $component->image_url }}" alt="{{ $component->name }}" class="w-full rounded shadow">
                </div>
            @endif

            {{-- Информация --}}
            <div class="flex-1 space-y-2">
                <p><strong>Категория:</strong> {{ $component->category->name }}</p>
                <p><strong>Бренд:</strong> {{ $component->brand ?? 'Не указан' }}</p>
                <p><strong>Цена:</strong> {{ number_format($component->price, 2, ',', ' ') }} $</p>

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
                {{-- Источники цен (если есть) --}}
                @if($component->parsedData->count())
                    <div>
                        <h2 class="text-lg font-semibold mt-4 mb-2">Цены в магазинах</h2>
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
        </div>
    </div>
</body>
</html>
