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