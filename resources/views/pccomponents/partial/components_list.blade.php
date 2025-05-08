<form method="POST" action="{{ route('delete', $component->id) }}" class="group">
    @csrf
    @method('DELETE')

    <div class="p-3 border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow flex sm:flex-row gap-3">
        <!-- Изображение -->
        <div class="w-full sm:w-24 h-24 flex-shrink-0 bg-gray-100 rounded-md overflow-hidden flex items-center justify-center">
            @php
                $imageUrl = $component->image_url 
                    ? asset('storage/products/' . basename($component->image_url)) 
                    : asset('images/defaulte_image.jpg');
            @endphp
            <img src="{{ $imageUrl }}"
                 alt="{{ $component->name }}"
                 class="w-full h-full object-contain group-hover:opacity-75 transition-opacity"
                 onerror="this.onerror=null; this.src='{{ asset('images/defaulte_image.jpg') }}';">
        </div>

        <!-- Контент -->
        <div class="flex-1 min-w-0">
            <div class="flex justify-between items-start gap-2 mb-1">
                <h3 class="text-sm font-medium line-clamp-2 leading-tight">{{ $component->name }}</h3>
                <span class="text-green-600 font-semibold text-sm whitespace-nowrap pl-2">
                    {{ number_format($component->price, 0, '', ' ') }} ₽
                </span>
            </div>

            <p class="text-xs text-gray-500 mb-2 truncate">{{ $component->brand }}</p>

            <!-- Характеристики -->
            @php
                $allChars = explode(',', $component->characteristics);
                $displayedChars = array_slice($allChars, 0, 10);
            @endphp

            <div class="text-xs flex flex-wrap gap-1 mb-2">
                @foreach($displayedChars as $char)
                    <span class="px-2 py-1 rounded ">{{ trim($char) }}</span>
                @endforeach

                @if(count($allChars) > 10)
                    <span class="px-2 py-1 rounded ">
                        +{{ count($allChars) - 10 }}
                    </span>
                @endif
            </div>


            <!-- Кнопки -->
            <div class="mt-2 pt-2 border-t border-gray-100 flex justify-between items-center gap-2">
                @if($component->shop_url)
                    <a href="{{ $component->shop_url }}" target="_blank"
                       class="text-xs text-blue-600 hover:text-blue-800 underline truncate max-w-[120px]">
                        Магазин
                    </a>
                @else
                    <span class="flex-1"></span>
                @endif

                <div class="flex items-center gap-2">
                    @if(auth()->check() && auth()->user()->admin == 1)
                        <a href="{{ route('components.show', $component->id) }}" 
                           class="text-xs text-indigo-600 hover:text-indigo-800 whitespace-nowrap">
                            Ред.
                        </a>
                        <button type="button" 
                                onclick="deleteComponent(event, {{ $component->id }})"
                                class="text-xs text-red-600 hover:text-red-800 whitespace-nowrap">
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
        </div>
    </div>
</form>
