@extends('layouts.navigation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="rounded-2xl shadow-md p-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold">{{ $build->name }}</h2>
                <p class="text-sm mt-1">Создано пользователем: <span class="font-semibold">{{ App\Models\User::find($build->user_id)->name }}</span></p>
                <p class="text-sm mt-1">Общая стоимость: <span class="font-semibold">{{ number_format($build->total_price, 2) }} ₽</span></p>
            </div>

            <div class="flex justify-center flex-wrap gap-6">
                @foreach($build->components as $component)
                    @php
                        $hasImage = $component->image_url;
                        $imagePath = $hasImage ? 'products/' . basename($component->image_url) : null;
                        $url = $hasImage ? asset('storage/' . $imagePath) : asset('images/defaulte_image.jpg');
                    @endphp

                    <div class="w-24 h-24 sm:w-32 sm:h-32 flex-shrink-0 bg-gray-50 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 hover:ring-2 hover:ring-blue-300 hover:ring-opacity-50">
                        <img 
                            src="{{ $url }}" 
                            alt="{{ $component->name }}" 
                            class="w-full h-full object-contain rounded-lg p-2"
                            onerror="this.onerror=null; this.src='{{ asset('images/defaulte_image.jpg') }}'"
                        >
                    </div>
                @endforeach
            </div>

            <div class="mt-8 text-center">
                <button class="accordion-toggle inline-flex items-center px-4 py-2 border border-blue-500 text-blue-500 rounded-lg hover:bg-blue-500 hover:text-white transition-all duration-300" 
                    aria-expanded="false" 
                    aria-controls="accordion-content-{{ $build->id }}">
                    Подробнее
                </button>
            </div>

            <!-- Скрытый контент -->
            <div id="accordion-content-{{ $build->id }}" class="accordion-content hidden mt-6">
                <ul class="space-y-4">
                    @foreach($build->components as $component)
                        <li class="flex justify-between items-center p-3rounded-lg shadow-sm hover:shadow-md transition">
                            <div class="flex-1 truncate">
                                <a href="{{ route('components.show', $component->id) }}" class="text-sm font-medium hover:text-blue-600 transition-colors">
                                    <span class="text-xs">{{ $component->category->name }}:</span>
                                    <span class="ml-1">{{ $component->name }}</span>
                                </a>
                            </div>
                            <div class="text-xs font-semibold text-green-600 whitespace-nowrap ml-4">
                                {{ number_format($component->price, 0, '', ' ') }} ₽
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.accordion-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const content = document.getElementById(this.getAttribute('aria-controls'));
            const isExpanded = this.getAttribute('aria-expanded') === 'true';

            content.classList.toggle('hidden', isExpanded);
            this.setAttribute('aria-expanded', !isExpanded);
        });
    });
</script>
@endsection
