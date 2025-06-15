<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ $component->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class= "min-h-screen">
    @include('layouts.navigation')

    <div class="container mx-auto px-4 py-8">
        @php
            $market = App\Models\Markets::find($component->market_id);
            $marketName = $market ? $market->name : 'Не указан';
        @endphp

        @if (auth()->check() && auth()->user()->admin == 1)
        <form action="{{ route('components.update', $component->id) }}" method="POST" enctype="multipart/form-data" class=" p-8 rounded-lg shadow-md space-y-6 max-w-4xl mx-auto">
            @csrf
            @method('PUT')

            {{-- Картинка --}}
            @if($component->image_url)
                <div class="flex justify-center">
                    @php
                        $imagePath = 'products/' . basename($component->image_url);
                        $url = asset('storage/' . $imagePath);
                    @endphp
                    <img src="{{ $url }}" alt="{{ $component->name }}" class="w-48 h-48 object-contain rounded shadow" onerror="this.onerror=null; this.src='{{ asset('images/defaulte_image.jpg') }}'">
                </div>
            @endif

            <div class="mb-4">
                <label for="image" class="block  font-semibold mb-2">Изменить изображение</label>
                <input type="file" name="image" id="image" class="block w-full text-sm  bg-gray-50 border border-gray-300 rounded p-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Информация --}}
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block  font-semibold mb-1">Категория</label>
                    <select name="category_id" id="category_id" required class="w-full p-2 border rounded bg-white text-black">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $component->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block  font-semibold mb-1">Название</label>
                    <input type="text" name="name" value="{{ $component->name }}" class="w-full p-2 border rounded bg-gray-50 text-black">
                </div>

                <div>
                    <label class="block  font-semibold mb-1">Бренд</label>
                    <input type="text" name="brand" value="{{ $component->brand }}" class="w-full p-2 border rounded bg-gray-50 text-black">
                </div>

                <div>
                    <label class="block  font-semibold mb-1">Цена (руб)</label>
                    <input type="number" step="0.01" name="price" value="{{ $component->price }}" class="w-full p-2 border rounded bg-gray-50 text-black">
                </div>

                <div>
                    <label class="block  font-semibold mb-1">Магазин</label>
                    <select id="market_id" name="market_id" required class="w-full p-2 border rounded bg-white text-black">
                        @foreach ($markets as $market)
                            <option value="{{ $market->id }}"{{ $component->market_id == $market->id ? 'selected' : '' }}>
                                {{ $market->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block  font-semibold mb-1">Ссылка на магазин</label>
                    <input type="url" name="shop_url" value="{{ $component->shop_url }}" class="w-full p-2 border rounded bg-gray-50 text-black">
                </div>

                <div>
                    <label class="block  font-semibold mb-1">Характеристики</label>
                    <textarea name="characteristics" class="w-full p-2 border rounded bg-gray-50 text-black h-40 resize-none">{{ str_replace(';', ";\n", $component->characteristics) }}</textarea>
                </div>

                <div>
                    <label class="block  font-semibold mb-1">Совместимость (JSON)</label>
                    <textarea name="compatibility_data" class="w-full p-2 border rounded bg-gray-50 text-black h-32 resize-none">{{ $component->compatibility_data }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Например: {"socket": "AM4", "form_factor": "ATX"}</p>
                </div>
            </div>
           
            <div class="flex justify-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">
                    Сохранить изменения
                </button>
            </div>
        </form>

        @else
        {{-- Если не админ --}}
        <div class="p-8  rounded-2xl shadow-2xl max-w-4xl mx-auto mt-10">
            <h1 class="text-3xl font-extrabold mb-8 text-center">{{ $component->name }}</h1>
            @if($component->parsedData->count())
                <div class="mt-6 text-center">
                    <a href="{{ route('components.chart', $component->id) }}"
                    class="inline-block bg-green-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-green-700 transition">
                        Посмотреть график цены
                    </a>
                </div>
            @endif
            @if($component->image_url)
                <div class="flex justify-center mb-8">
                    @php
                        $imagePath = 'products/' . basename($component->image_url);
                        $url = asset('storage/' . $imagePath);
                    @endphp
                    <img src="{{ $url }}" alt="{{ $component->name }}" class="w-60 h-60 object-contain rounded-xl shadow-lg border" 
                         onerror="this.onerror=null; this.src='{{ asset('images/defaulte_image.jpg') }}'">
                </div>
            @endif
        
            <div class="space-y-6  text-lg">
                <div>
                    <span class="font-semibold ">Категория:</span> {{ $component->category->name }}
                </div>
                <div>
                    <span class="font-semibold ">Магазин:</span> {{ $marketName }}
                </div>
                <div>
                    <span class="font-semibold ">Бренд:</span> {{ $component->brand ?? 'Не указан' }}
                </div>
                <div>
                    <span class="font-semibold ">Цена:</span> {{ number_format($component->price, 2, ',', ' ') }} <span class="text-sm">руб</span>
                </div>
                <div>
                    <p><strong>Характеристики:</strong><br>{{ str_replace(';', ";\n", $component->characteristics) }}</p>
                </div>
        
                @if($component->shop_url)
                    <div class="text-center">
                        <a href="{{ $component->shop_url }}" target="_blank" 
                           class="inline-block bg-blue-600 text-white font-medium px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                            Перейти в магазин
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        @endif

        {{-- Источники цен --}}
        @php
        $lastParsed = $component->parsedData->sortByDesc('updated_at')->first();
    @endphp
    
    @if($lastParsed)
        <div class="mt-10 p-6 rounded-lg shadow-md max-w-4xl mx-auto">
            <h2 class="text-lg font-semibold mb-4">Последняя цена</h2>
            <ul class="list-disc space-y-2 ml-5">
                <li>
                    <strong>{{ ucfirst($lastParsed->source) }}:</strong>
                    {{ number_format($lastParsed->price, 2, ',', ' ') }} ₽
                    {{ $lastParsed->availability ? '(В наличии)' : '(Нет в наличии)' }}
                    <br>
                    <span class="text-sm text-gray-500">
                        Обновлено: {{ $lastParsed->updated_at->format('d.m.Y H:i') }}
                    </span>
                </li>
            </ul>
        </div>
    @endif
    </div>
</body>
</html>
