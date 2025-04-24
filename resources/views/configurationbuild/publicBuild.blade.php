@extends('layouts.navigation')  

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mt-6 overflow-x-auto">
        <div class="grid grid-cols-2 md:grid-cols-1 lg:grid-cols-2 gap-6">
           
            <div class="accordion-item border rounded-lg p-4 shadow mb-4">
               
             
                <h2 class="text-lg font-semibold">{{ $build->name }}</h2>
                <h1>Пользователь {{ App\Models\User::find($build->user_id)->name }}</h1> 
                <p class="text-sm text-gray-500">Общая стоимость: {{ number_format($build->total_price, 2) }} руб</p>
                 
                <div class="flex justify-center flex-wrap gap-4 mt-4">
                    @foreach($build->components as $component)
                        @php
                            $hasImage = $component->image_url;
                            $imagePath = $hasImage ? 'products/' . basename($component->image_url) : null;
                            $url = $hasImage ? asset('storage/' . $imagePath) : asset('images/defaulte_image.jpg');
                        @endphp
        
                        <div class="w-48 h-48 flex-shrink-0">
                            <img 
                                src="{{ $url }}" 
                                alt="{{ $component->name }}" 
                                class="w-full h-full object-contain rounded shadow border"
                                onerror="this.onerror=null; this.src='{{ asset('images/defaulte_image.jpg') }}'"
                            >
                        </div>
                    @endforeach
                </div>
        
                <button class="accordion-toggle mt-2 text-blue-500 hover:underline" aria-expanded="false" aria-controls="accordion-content-{{ $build->id }}">
                    Подробнее
                </button>
        
                <!-- Скрытый контент -->
                <div id="accordion-content-{{ $build->id }}" class="accordion-content hidden mt-3">
                    <ul class="space-y-2 w-full mx-auto" style = "width: 65%">
                        @foreach($build->components as $component)
                        <li class="py-2 border-b border-gray-200 last:border-0" >
                            <div class="flex justify-between items-baseline">
                                <div class="truncate pr-2">
                                    <a href="{{ route('components.show', $component->id) }}" 
                                        class="text-sm hover:text-blue-600 transition-colors">
                                         <span class="text-xs">{{ $component->category->name }}:</span>
                                         <span class="ml-1 font-medium">{{ $component->name }}</span>
                                     </a>
                                </div>
                                <span class="text-xs font-medium text-green-600 whitespace-nowrap">
                                    {{ number_format($component->price, 0, '', ' ') }}₽
                                </span>
                            </div>
                           
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
           
        </div>
        
    </div>
</div>
    <script>
        document.querySelectorAll('.accordion-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const content = document.getElementById(this.getAttribute('aria-controls'));
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
    
                // Переключаем видимость контента
                content.style.display = isExpanded ? 'none' : 'block';
    
                // Обновляем aria-expanded для доступности
                this.setAttribute('aria-expanded', !isExpanded);
            });
        });
    </script>
@endsection