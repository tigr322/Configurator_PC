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
                <div id="accordion-content-{{ $build->id }}" class="accordion-content hidden mt-2">
                    <ul class="list-disc pl-5">
        
                        @foreach($build->components as $component)
                        
                            <li>
                                
                                <strong>{{ $component->category->name }}:</strong> 
                                {{ $component->name }} — {{ number_format($component->price, 2) }} руб
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