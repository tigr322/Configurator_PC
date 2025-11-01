@extends('layouts.navigation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="rounded-2xl shadow-md p-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold">{{ $build->name }}</h2>
                <p class="text-sm mt-1">Автор сборки: <span class="font-semibold">{{ App\Models\User::find($build->user_id)->name }}</span></p>
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
                    Подробнее о комплектующих
                </button>
            </div>

            <div id="accordion-content-{{ $build->id }}" class="accordion-content hidden mt-6">
                <ul class="space-y-4">
                    @foreach($build->components as $component)
                        <li class="flex justify-between items-center p-3 rounded-lg shadow-sm hover:shadow-md transition">
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

        @php
            $__aiComponents = [];
            if (isset($build) && $build && isset($build->components)) {
                foreach ($build->components as $c) {
                    $__aiComponents[] = [
                        'category' => optional($c->category)->name,
                        'name' => $c->name,
                        'price' => $c->price,
                    ];
                }
            }
            $__aiMeta = [
                'build_id' => isset($build) ? $build->id : null,
                'build_name' => isset($build) ? $build->name : null,
                'total_price' => isset($build) ? $build->total_price : null,
                'currency' => '₽',
            ];
        @endphp

        <div class="mt-6 text-center">
            <button
                id="ai-ask-build"
                type="button"
                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors"
                data-build-id="{{ $build->id }}"
                data-ai-components='@json($__aiComponents)'
                data-ai-meta='@json($__aiMeta)'
            >
                Спросить о сборке ИИ
            </button>
        </div>

        <div id="ai-result-{{ $build->id }}" class="mt-4 hidden">
            <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 text-sm text-gray-800 shadow-sm">
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="font-semibold text-indigo-700">Ответ ИИ</h3>
                    <button type="button" class="text-xs text-indigo-600 hover:text-indigo-800" onclick="document.getElementById('ai-result-{{ $build->id }}').classList.add('hidden')">Скрыть</button>
                </div>
                <div id="ai-result-content-{{ $build->id }}" class="whitespace-pre-wrap leading-relaxed"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // Аккордеон
    document.querySelectorAll('.accordion-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const content = document.getElementById(this.getAttribute('aria-controls'));
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            content.classList.toggle('hidden', isExpanded);
            this.setAttribute('aria-expanded', !isExpanded);
        });
    });

    // AI: собрать промпт и отправить
    (function () {
        function formatPrice(n) {
            const num = Number(n);
            if (isNaN(num)) { return n; }
            return num.toLocaleString('ru-RU');
        }

        function buildPrompt(components, meta) {
            const base = 'Оцени совместимость и оптимальность данной сборки';
            const header = [];
            if (meta && (meta.build_name || meta.total_price)) {
                if (meta.build_name) {
                    header.push('Сборка: ' + meta.build_name + (meta.build_id ? (' (ID ' + meta.build_id + ')') : ''));
                }
                if (meta.total_price) {
                    header.push('Общая стоимость: ' + formatPrice(meta.total_price) + (meta.currency ? (' ' + meta.currency) : ''));
                }
            }
            const lines = (components || []).map(function (c) {
                const leftParts = [];
                if (c.category) { leftParts.push(c.category); }
                if (c.name) { leftParts.push(c.name); }
                const left = leftParts.join(': ');
                const right = (c.price !== undefined && c.price !== null && c.price !== '') ? (' — ' + formatPrice(c.price) + (meta && meta.currency ? (' ' + meta.currency) : '')) : '';
                return '- ' + left + right;
            });
            const blocks = [base];
            if (header.length) { blocks.push(header.join('\n')); }
            if (lines.length) { blocks.push('Компоненты:\n' + lines.join('\n')); }
            return blocks.join('\n\n');
        }

        function postToAi(prompt, components, meta) {
            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            const token = tokenEl ? tokenEl.getAttribute('content') : '';
            return fetch("{{ route('ai.generate') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ prompt: prompt, components: components, meta: meta })
            });
        }

        const askBtn = document.getElementById('ai-ask-build');
        if (askBtn) {
            askBtn.addEventListener('click', function () {
                try {
                    const comps = JSON.parse(askBtn.getAttribute('data-ai-components') || '[]');
                    const meta = JSON.parse(askBtn.getAttribute('data-ai-meta') || '{}');
                    const prompt = buildPrompt(comps, meta);
                    const originalText = askBtn.textContent;
                    const buildId = askBtn.getAttribute('data-build-id');
                    const resultBox = document.getElementById('ai-result-' + buildId);
                    const resultContent = document.getElementById('ai-result-content-' + buildId);
                    if (resultBox && resultContent) {
                        resultContent.textContent = 'Обрабатываю запрос...';
                        resultBox.classList.remove('hidden');
                    }
                    askBtn.textContent = 'Отправка...';
                    askBtn.disabled = true;
                    postToAi(prompt, comps, meta)
                        .then(function (res) {
                            if (!res.ok) { throw new Error('AI route status ' + res.status); }
                            return res.json().catch(function () { return {}; });
                        })
                        .then(function (data) {
                            try {
                                const text = (data && (data.text || data.reply)) ? (data.text || data.reply) : JSON.stringify(data);
                                if (resultContent) { resultContent.textContent = text || 'Пустой ответ от ИИ'; }
                            } catch (e) {
                                if (resultContent) { resultContent.textContent = 'Не удалось отобразить ответ ИИ.'; }
                            }
                            askBtn.textContent = 'Отправлено в ИИ';
                            setTimeout(function () { askBtn.textContent = originalText; askBtn.disabled = false; }, 1500);
                        })
                        .catch(function (err) {
                            if (resultContent) { resultContent.textContent = 'Ошибка: не удалось получить ответ ИИ.'; }
                            askBtn.textContent = 'Ошибка отправки';
                            setTimeout(function () { askBtn.textContent = originalText; askBtn.disabled = false; }, 1500);
                        });
                } catch (e) {
                    console.error(e);
                }
            });
        }
    })();
</script>
@endsection

