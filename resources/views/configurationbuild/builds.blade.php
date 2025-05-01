<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="//unpkg.com/alpinejs" defer></script>

    <title>Каталог конфигурации</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .accordion-content {
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.3s ease;
}

.accordion-content.open {
    max-height: 1000px; /* Больше чем содержимое, чтобы всё влезло */
}

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
    class="mb-10 max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-5 gap-4 items-center p-6 rounded-2xl shadow">

    <input 
        type="text" 
        name="search" 
        placeholder="Название сборки" 
        value="{{ request('search') }}"
        class="w-full px-4 py-2 text-sm text-black bg-gray-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent focus:outline-none"
    >

    <input 
        type="text" 
        name="component" 
        placeholder="Название компонента" 
        value="{{ request('component') }}"
        class="w-full px-4 py-2 text-sm text-black bg-gray-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent focus:outline-none"
    >

    <input 
        type="number" 
        name="pagination" 
        placeholder="Количество на странице" 
        value="{{ request('pagination') }}"
        class="w-full px-4 py-2 text-sm text-black bg-gray-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent focus:outline-none"
    >

    <select 
        name="sort"
        class="w-full px-4 py-2 text-sm text-black bg-gray-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent focus:outline-none"
    >
        <option value="">Сортировка</option>
        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Цена ↑</option>
        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Цена ↓</option>
    </select>
    <select 
    name="like"
    onchange="this.form.submit()" 
    class="w-full px-4 py-2 text-sm text-black bg-gray-100 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-transparent focus:outline-none"
>
    <option value="">Сортировать по</option>
    <option value="like" {{ request('like') === 'like' ? 'selected' : '' }}>По лайкам</option>
    <option value="dislike" {{ request('like') === 'dislike' ? 'selected' : '' }}>По дизлайкам</option>
</select>


    <div class="flex justify-center">
        <button 
            type="submit"
            class="w-full md:w-auto px-6 py-2 text-sm font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-all"
        >
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

<div class="w-24 h-24 sm:w-32 sm:h-32 flex-shrink-0 rounded-lg shadow-sm hover:shadow-md transition-shadow hover:ring-2 hover:ring-blue-400 hover:ring-opacity-50">

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
            <div class="card p-4 rounded-xl shadow-md w-full">
                <div class="flex justify-between items-center cursor-pointer" onclick="toggleAccordion(this)">
                 
                    <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            
                <div id="accordion-content-{{ $build->id }}" class="accordion-content overflow-hidden max-h-0 transition-all duration-500 ease-in-out">
                    <ul class="space-y-2 w-full mx-auto">
                        @foreach($build->components as $component)
                            <li class="py-2 border-b border-gray-200 last:border-0">
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
            <div x-data="{ showComments: false }" class="max-w-4xl mx-auto my-6">
            <h2>Комментарии:</h2>
            <button @click="showComments = !showComments"
            class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 focus:outline-none mb-4">
            <span x-show="!showComments">Показать комментарии</span>
            <span x-show="showComments">Скрыть комментарии</span>
            </button>
            <div x-show="showComments" x-transition class="space-y-4">
            @foreach ($build->comments as $comment)
                <div>
                    <strong>{{ $comment->user->name }}</strong> написал:
                    <p>{{ $comment->body }}</p>
                    <small>{{ $comment->created_at->diffForHumans() }}</small>
                    @if (auth()->id() === $comment->user_id || (auth()->check() && auth()->user()->admin == 1))
                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit"
                            onclick="return confirm('Удалить комментарий?')"
                            class="px-3 py-1 text-sm font-medium text-red-600 bg-red-100 border border-red-300 rounded-md hover:bg-red-200 hover:text-red-700 transition-all"
                        >
                            Удалить
                        </button>
                    </form>
                @endif
                
              
                </div>
            @endforeach
        </div>
    </div>
            <div class="mt-2 text-sm" id="vote-counts-{{ $build->id }}">
                👍 Лайков: <span id="likes-{{ $build->id }}">{{ $build->likes()->count() }}</span> |
                👎 Дизлайков: <span id="dislikes-{{ $build->id }}">{{ $build->dislikes()->count() }}</span> |
                🏆 Голосов за лучшую сборку: <span id="best-{{ $build->id }}">{{ $build->bestBuildVotes()->count() }}</span>
            </div>
            @if (auth()->check())
              
           
            <form method="POST" action="{{ route('comments.store') }}" class="max-w-xl mx-auto p-6 rounded-2xl shadow-md">
                @csrf
                <input type="hidden" name="configuration_id" value="{{ $build->id }}">
            
                <div class="mb-4">
                    <label for="body" class="block text-sm font-semibold mb-2">Ваш комментарий:</label>
                    <textarea name="body" id="body" rows="4" required
                        class="w-full text-black px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        placeholder="Введите ваш комментарий..."></textarea>
                </div>
            
                <div class="text-right">
                    <button type="submit"
                        class="bg-blue-500 text-white hover:bg-blue-600 font-semibold py-2 px-6 rounded-lg transition duration-300">
                        Добавить комментарий
                    </button>
                </div>
            </form>
            <div class="flex gap-4 mt-4" id="votes-{{ $build->id }}">
                <button 
                    class="vote-button px-3 py-1  text-green-700 rounded hover:bg-green-200" 
                    onclick="vote('/configurations/{{ $build->id }}/like')" 
                    data-type="like">
                    👍 Лайк
                </button>
            
                <button 
                    class="vote-button px-3 py-1 text-red-700 rounded hover:bg-red-200" 
                    onclick="vote('/configurations/{{ $build->id }}/dislike')"
                    data-type="dislike">
                    👎 Дизлайк
                </button>
            
                <button 
                    class="vote-button px-3 py-1 text-yellow-700 rounded hover:bg-yellow-200" 
                   onclick="vote('/configurations/{{ $build->id }}/best')"
                    data-type="best">
                    🏆 За лучшую сборку
                </button>
            </div>
            @endif
            
            
        </div>

        <!-- Скрытый контент (список компонентов) -->
       
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
   <script>
   async function vote(url) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ошибка! Статус: ${response.status}`);
        }

        const data = await response.json();

        const buildId = data.build_id;

        document.getElementById(`likes-${buildId}`).textContent = data.likes;
        document.getElementById(`dislikes-${buildId}`).textContent = data.dislikes;
        document.getElementById(`best-${buildId}`).textContent = data.best_votes;

    } catch (error) {
        console.error('Ошибка голосования:', error);
        alert('Произошла ошибка при голосовании. Попробуйте позже.');
    }
}

    </script>
    <script>
        function toggleAccordion(header) {
    const card = header.parentElement;
    const content = card.querySelector('.accordion-content');
    const icon = header.querySelector('svg');

    if (content.classList.contains('open')) {
        content.style.maxHeight = null;
        content.classList.remove('open');
        icon.style.transform = 'rotate(0deg)';
    } else {
        content.style.maxHeight = content.scrollHeight + 'px';
        content.classList.add('open');
        icon.style.transform = 'rotate(180deg)';
    }
}

        </script>
</body>
</html>