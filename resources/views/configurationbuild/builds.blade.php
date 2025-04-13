<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог конфигурации</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>


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

<div class="grid grid-cols-2 md:grid-cols-1 lg:grid-cols-2 gap-6">
    @forelse ($builds as $build)
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

        <!-- Кнопки управления -->
      
            <form action="{{ route('builds.destroy', $build->id) }}" method="POST" onsubmit="return confirm('Удалить эту конфигурацию?');">
                @csrf
                @method('DELETE')
                @if (auth()->check() && auth()->user()->admin == 1)
                <button type="submit" class="mt-2 text-red-600 hover:underline">Удалить</button>
                @endif
            </form>
       
    </div>
    @empty
        <p>Конфигурации не найдены.</p>
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

</body>
</html>

