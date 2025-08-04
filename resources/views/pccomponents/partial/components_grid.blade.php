<form method="POST" action="{{ route('delete', $component->id) }}" class="group">
    @csrf
    @method('DELETE')

    <div class="border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
       
        <div class="aspect-square w-full rounded-lg bg-gray-100 flex items-center justify-center mb-4 overflow-hidden">
            @php
                $imageUrl = $component->image_url 
                    ? asset('storage/products/' . basename($component->image_url)) 
                    : asset('images/defaulte_image.jpg');
            @endphp
            <img src="{{ $imageUrl }}"
                 alt="{{ $component->name }}"
                 class="w-full h-full object-contain group-hover:opacity-75"
                 onerror="this.onerror=null; this.src='{{ asset('images/defaulte_image.jpg') }}';">
        </div>

       
        <div class="flex-grow">
            <h3 class="text-sm font-medium line-clamp-2 mb-1">{{ $component->name }}</h3>
            <p class="text-xs text-gray-500">{{ $component->brand }}</p>
        </div>

       
        <div class="mt-4">
            <p class="text-lg font-medium text-green-600">{{ number_format($component->price, 0, '', ' ') }} ₽</p>
            <a href="{{ route('components.show', $component->id) }}" 
               class="text-sm font-medium text-blue-600 hover:text-blue-500">
                Подробнее
            </a>

            @if (auth()->check() && auth()->user()->admin == 1)
                <button type="button" 
                        onclick="deleteComponent(event, {{ $component->id }})" 
                        class="text-red-600 hover:text-red-800 text-sm font-medium">
                    Удалить
                </button>
            @endif

            @if (session('configurator_mode') == true)
            <div class="component-card">
                <button type="button" 
                    onclick="addToConfiguration({{ $component->id }}, '{{ $component->name }}', '{{ $imageUrl }}', {{ $component->category_id }})"
                    class="add-to-config-btn px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition-colors"
                    data-component-id="{{ $component->id }}">
                    Добавить в сборку
                </button>
                <p class="incompatible-text text-red-600 text-xs mt-1 hidden text-center">
                    Несовместим с текущей сборкой
                </p>
            </div>
        @endif
        
        

        </div>
    </div>
</form>
