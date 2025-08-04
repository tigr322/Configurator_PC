<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ $component->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.png') }}">
</head>
<body class="min-h-screen">
    @include('layouts.navigation')

    <div class="container mx-auto px-4 py-8">
        @php
            $market = App\Models\Markets::find($component->market_id);
            $marketName = $market ? $market->name : 'Не указан';
            $lastParsed = $component->parsedData->sortByDesc('updated_at')->first();
            $isAdmin = auth()->check() && auth()->user()->admin == 1;
            $viewMode = request()->query('view', 'auto'); // admin, user, auto
            if ($viewMode === 'auto') {
                $viewMode = $isAdmin ? 'admin' : 'user';
            }
        @endphp

        @if ($isAdmin)
            {{-- Переключатель режима --}}
            <div class="mb-6 text-right">
                <a href="{{ route('components.show', [$component->id, 'view' => $viewMode === 'admin' ? 'user' : 'admin']) }}"
                   class="text-sm text-blue-600 hover:underline">
                    Переключить на {{ $viewMode === 'admin' ? 'пользовательский' : 'админский' }} режим
                </a>
            </div>
        @endif

        @if ($viewMode === 'admin')
            {{-- 🛠 Админская форма редактирования --}}
            @if($component->parsedData->count())
                    <div class="mt-6 text-center">
                        <a href="{{ route('components.chart', $component->id) }}"
                           class="inline-block bg-green-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-green-700 transition shadow">
                            Посмотреть график цены
                        </a>
                    </div>
                @endif
            <form action="{{ route('components.update', $component->id) }}" method="POST" enctype="multipart/form-data"
                  class=" p-8 rounded-lg shadow-md space-y-6 max-w-4xl mx-auto">
                @csrf
                @method('PUT')

                {{-- Картинка --}}
                @if($component->image_url)
                    <div class="flex justify-center">
                        @php
                            $imagePath = 'products/' . basename($component->image_url);
                            $url = asset('storage/' . $imagePath);
                        @endphp
                        <img src="{{ $url }}" alt="{{ $component->name }}"
                             class="w-48 h-48 object-contain rounded shadow"
                             onerror="this.onerror=null; this.src='{{ asset('images/defaulte_image.jpg') }}'">
                    </div>
                @endif

                <div>
                    <label for="image" class="block font-semibold mb-2">Изменить изображение</label>
                    <input type="file" name="image" id="image"
                           class="block w-full text-sm bg-white text-black border  rounded p-2">
                </div>

                {{-- Поля --}}
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="font-semibold block mb-1">Категория</label>
                        <select name="category_id" class="w-full p-2 border rounded bg-white text-black">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $component->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="font-semibold block mb-1">Название</label>
                        <input type="text" name="name" value="{{ $component->name }}"
                               class="w-full p-2 border rounded bg-white text-black">
                    </div>

                    <div>
                        <label class="font-semibold block mb-1">Бренд</label>
                        <input type="text" name="brand" value="{{ $component->brand }}"
                               class="w-full p-2 border rounded bg-white text-black">
                    </div>

                    <div>
                        <label class="font-semibold block mb-1">Цена (руб)</label>
                        <input type="number" step="0.01" name="price" value="{{ $component->price }}"
                               class="w-full p-2 border rounded bg-white text-black">
                    </div>

                    <div>
                        <label class="font-semibold block mb-1">Магазин</label>
                        <select name="market_id" class="w-full p-2 border rounded bg-white text-black">
                            @foreach ($markets as $market)
                                <option value="{{ $market->id }}" {{ $component->market_id == $market->id ? 'selected' : '' }}>
                                    {{ $market->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="font-semibold block mb-1">Ссылка на магазин</label>
                        <input type="url" name="shop_url" value="{{ $component->shop_url }}"
                               class="w-full p-2 border rounded bg-white text-black">
                    </div>

                    <div>
                        <label class="font-semibold block mb-1">Характеристики</label>
                        <textarea name="characteristics"
                                  class="w-full p-2 border rounded bg-white text-black h-40 resize-none">{{ str_replace(';', ";\n", $component->characteristics) }}</textarea>
                    </div>

                    <div>
                        <label class="font-semibold block mb-1">Совместимость (JSON)</label>
                        <textarea name="compatibility_data"
                                  class="w-full p-2 border rounded bg-white text-black h-32 resize-none">{{ $component->compatibility_data }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Например: {"socket": "AM4", "form_factor": "ATX"}</p>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">
                        Сохранить изменения
                    </button>
                </div>
            </form>
        @else
            {{-- 👤 Пользовательский просмотр --}}
            <div class="max-w-5xl mx-auto mt-4  rounded-2xl shadow-xl p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Изображение + кнопка --}}
                    <div class="flex flex-col items-center gap-6">
                        @php
                            $imagePath = 'products/' . basename($component->image_url);
                            $url = asset('storage/' . $imagePath);
                        @endphp
                        <img src="{{ $url }}" alt="{{ $component->name }}"
                             class="w-72 h-72 object-contain rounded-xl shadow border"
                             onerror="this.onerror=null; this.src='{{ asset('images/defaulte_image.jpg') }}'">

                        @if($component->shop_url)
                            <a href="{{ $component->shop_url }}" target="_blank"
                               class="bg-blue-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-700 transition shadow">
                                Перейти в магазин
                            </a>
                        @endif
                    </div>

                    {{-- Характеристики --}}
                    <div class="space-y-4">
                        <h1 class="text-3xl font-extrabold">{{ $component->name }}</h1>
                        <div  class="font-medium"><span>Категория:</span> {{ $component->category->name }}</div>
                        <div><span class="font-medium">Магазин:</span> {{ $marketName }}</div>
                        <div><span class="font-medium">Бренд:</span> {{ $component->brand ?? 'Не указан' }}</div>
                        <div class="text-2xl text-green-600 font-bold mt-4">
                            {{ number_format($component->price, 0, ',', ' ') }} ₽
                        </div>
                        
                    </div>
                    <div class="mt-6">
                        <h2 class="font-semibold mb-2">Характеристики:</h2>
                        <div class="whitespace-pre-line text-sm  p-4 rounded border">
                            {{ str_replace(';', ";\n", $component->characteristics) }}
                        </div>
                    </div>
                </div>

                {{-- Последняя цена --}}
                @if($lastParsed)
                    <div class="mt-10 bg-gray-50 p-4 rounded-lg border text-sm text-gray-600">
                        <strong>Источник:</strong> {{ ucfirst($lastParsed->source) }}<br>
                        <strong>Доступность:</strong> {{ $lastParsed->availability ? 'В наличии' : 'Нет в наличии' }}<br>
                        <strong>Обновлено:</strong> {{ $lastParsed->updated_at->format('d.m.Y H:i') }}
                    </div>
                @endif

                {{-- Кнопка графика --}}
                @if($component->parsedData->count())
                    <div class="mt-6 text-center">
                        <a href="{{ route('components.chart', $component->id) }}"
                           class="inline-block bg-green-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-green-700 transition shadow">
                            Посмотреть график цены
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</body>
</html>