<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог конфигурации</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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
</head>

<body>
@include('layouts.navigation')  

<div class="container mx-auto px-4 py-3">
    <form method="GET" action="{{ route('configurations') }}"
    class="mb-6 max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-5 gap-4 items-center justify-center">
  <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;"
         type="text" name="search" placeholder="Название" value="{{ request('search') }}" class="border p-2 rounded">
  
  <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;"
         type="text" name="component" placeholder="Компонент" value="{{ request('component') }}" class="border p-2 rounded">
  <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;"
         type="number" name="pagination" placeholder="Пагинация" value="{{ request('pagination') }}" class="border p-2 rounded">
  <select style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;"
          name="sort" class="border p-2 rounded">
      <option value="">Сортировка</option>
      <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Цена ↑</option>
      <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Цена ↓</option>
  </select>
  
  <div class=" flex justify-center">
    <button type="submit" class="inline-block px-5 py-1.5 border border-transparent hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
        Применить
    </button>
</div>

</form>

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

            @if (auth()->check() && auth()->user()->admin == 1)
            <form action="{{ route('builds.destroy', $build->id) }}" method="POST" 
                  onsubmit="return confirm('Удалить эту конфигурацию?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm sm:text-base text-red-600 hover:text-red-800 transition-colors">
                    Удалить
                </button>
            </form>
            @endif
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

<div class="mt-6">
    {{ $col->withQueryString()->links() }}
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
   
</body>
</html>

