<section>
    <header>
        <h2 class="text-lg font-medium">
            {{ __('Мои конфигурации') }}
        </h2>
        <p class="mt-1 text-sm">
            {{ __('Вы можете удалить или редактировать свои конфигурации!') }}
        </p>
        <style>
            /* Медиазапросы для тонкой настройки */
            @media (max-width: 640px) {
                .accordion-item {
                    padding: 0.75rem;
                }
                .accordion-content ul {
                    width: 100%;
                }
            }
            @media (min-width: 641px) and (max-width: 1023px) {
                .accordion-content ul {
                    width: 80%;
                }
            }
        </style>
    </header>
    <div class="mt-6 overflow-x-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
            @forelse ($builds as $build)
            <div class="accordion-item border rounded-lg p-3 sm:p-4 shadow mb-4 transition-all duration-200">
                <!-- Заголовок -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h2 class="text-base sm:text-lg font-semibold">{{ $build->name }}</h2>
                        <p class="text-xs sm:text-sm">Пользователь: {{ App\Models\User::find($build->user_id)->name }}</p>
                    </div>
                    <p class="text-sm sm:text-base font-medium">Итого: {{ number_format($build->total_price, 2) }} руб</p>
                </div>
        
                <!-- Галерея компонентов -->
                <div class="flex overflow-x-auto sm:overflow-visible sm:justify-center sm:flex-wrap gap-3 mt-3 py-2 sm:py-0">
                    @foreach($build->components as $component)
                        @php
                            $hasImage = $component->image_url;
                            $imagePath = $hasImage ? 'products/' . basename($component->image_url) : null;
                            $url = $hasImage ? asset('storage/' . $imagePath) : asset('images/defaulte_image.jpg');
                        @endphp
        
                        <div class="w-24 h-24 sm:w-32 sm:h-32 flex-shrink-0">
                            <img 
                                src="{{ $url }}" 
                                alt="{{ $component->name }}" 
                                class="w-full h-full object-contain rounded shadow border border-gray-200"
                                onerror="this.onerror=null; this.src='{{ asset('images/defaulte_image.jpg') }}'"
                            >
                        </div>
                    @endforeach
                </div>
        
                <!-- Кнопки управления -->
                <div class="flex-wrap gap-3 mt-3">
                    <button class="accordion-toggle text-sm sm:text-base text-blue-500 hover:text-blue-700 transition-colors" 
                            aria-expanded="false" 
                            aria-controls="accordion-content-{{ $build->id }}">
                        Подробнее
                    </button>
                    
                    <button onclick="copyShareLink({{ $build->id }})" 
                            class="text-sm sm:text-base text-green-600 hover:text-green-800 transition-colors">
                        Поделиться
                    </button>
        
                  
                    <form action="{{ route('builds.destroy', $build->id) }}" method="POST" 
                          onsubmit="return confirm('Удалить эту конфигурацию?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm sm:text-base text-red-600 hover:text-red-800 transition-colors">
                            Удалить
                        </button>
                    </form>
                    
                </div>
        
                <!-- Скрытый контент (список компонентов) -->
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
            @empty
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500">Конфигурации не найдены.</p>
            </div>
            @endforelse
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
    <script>
        function copyShareLink(buildId) {
            const url = `{{ url('/public-build') }}/${buildId}`;
            navigator.clipboard.writeText(url)
                .then(() => alert('Ссылка скопирована в буфер обмена!'))
                .catch(() => alert('Ошибка при копировании ссылки'));
        }
        </script>
        
</section>