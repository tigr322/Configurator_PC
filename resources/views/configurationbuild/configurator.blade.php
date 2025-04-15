@extends('layouts.navigation')  
@section('title', 'Создать конфигурацию')

@section('content')
<!--<style>
    
    .container {
        display: flex;
        flex-direction: column;
        align-items: flex-start; /* Прижимаем слева */
        padding-left: 30px; /* Отступ от левого края */
        margin-left: 0;
        max-width: 2000px; /* Максимальная ширина контейнера */
        width: 100%;
    }
    
    .container h1 {
        margin-bottom: 20px;
    }

    .container form {
        width: 20%;
        background-color: #000000;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .mb-3 {
        margin-bottom: 15px;
    }

    .form-control {
        background-color: #f3f4f6;
        padding: 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        color: black;
    }

    button {
        padding: 0.75rem 1.5rem;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #45a049;
    }

    /* Стиль для сообщений об успехе или ошибке */
    .alert {
        font-weight: bold;
        text-align: center;
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 4px;
        width: 100%;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
    }

</style> -->

    <div class="container">

        <h1 style="font-size: x-large">Создание конфигурации ПК</h1>
       @if (session('success'))
<div style="color: green; font-weight: bold; text-align: center;">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
    <div style="color: red; font-weight: bold; text-align: center;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <form action="{{ route('configurations.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="mb-4">
                <label for="config-name" class="block mb-1">Название конфигурации:</label>
                <input type="text" name="name" id="config-name" 
                       class=" md:w-1/2 bg-gray-100 p-2 rounded text-sm text-black" required>
            </div>

            <h2 class="text-lg font-medium mb-3">Выберите комплектующие:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($categories as $category)
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 p-3 border border-gray-200 rounded-lg">
                <div class="flex-1 min-w-0">
                    <label for="component_{{ $category->id }}">{{ $category->name }}:</label>
                    <select style="max-width: 200px; background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem;  color: black;" name="components[{{ $category->id }}]" id="component_{{ $category->id }}" class="w-full bg-gray-100 p-2 rounded text-sm text-black">
                        <option value="">-- Не выбрано --</option>
                        @foreach($category->components as $component)
                        <option 
                        value="{{ $component->id }}" 
                        data-image-url="{{ asset('storage/products/' . basename($component->image_url)) }}"
                    >
                                {{ $component->name }} ({{ number_format($component->price, 2) }} руб)
                            </option>
                        
                        @endforeach
                    </select>
                </div>
                <img 
                id="preview_image_{{ $category->id }}" 
                src="{{ asset('images/defaulte_image.jpg') }}" 
                alt="Предпросмотр"
                style="width: 175px; height: 150px; object-fit: contain;"
                class="rounded shadow border border-gray-300 inline-block ml-4 align-middle"
                />
            </div>
            @endforeach
        </div>
            <div class="py-1.5 px-5 mb-4">

            <button type="submit" class="inline-block px-5 py-1.5 border border-transparent hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                Создать конфигурацию
            </button>
            </div>
        </form>
       
    </div>
   
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selects = document.querySelectorAll('select[id^="component_"]');
        
            selects.forEach(select => {
                select.addEventListener('change', function () {
                checkAllCompatibility();
                updateImagePreview(select);
            });

            // Первичная инициализация изображений (если надо)
            updateImagePreview(select);
        });
        function updateImagePreview(select) {
            const selectedOption = select.options[select.selectedIndex];
            const imageUrl = selectedOption.dataset.imageUrl || '{{ asset('images/defaulte_image.jpg') }}';
            const categoryId = select.id.replace('component_', '');
            const imgElement = document.getElementById(`preview_image_${categoryId}`);

            if (imgElement) {
                imgElement.src = imageUrl;
            }
        }
        
            function checkAllCompatibility() {
                // Собираем все выбранные компоненты
                const selectedComponents = {};
                selects.forEach(select => {
                    const categoryId = select.id.replace('component_', '');
                    const componentId = select.value;
                    if (componentId) {
                        selectedComponents[categoryId] = componentId;
                    }
                });
        
                //if (Object.keys(selectedComponents).length === 0) return;
        
                fetch('/configurator/check-compatibility-multi', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        selected_components: selectedComponents
                    })
                })
                .then(response => {
    if (!response.ok) {
        return response.json().then(error => { throw error; });
    }
    return response.json();
})
                .then(data => {
                    // Сначала разблокируем все опции
                    selects.forEach(select => {
                        Array.from(select.options).forEach(option => {
                            if (option.value !== "") {
                                option.disabled = false;
                            }
                        });
                    });
        
                    // Проходим по каждой категории с несовместимыми компонентами
                    for (const [categoryId, incompatibleIds] of Object.entries(data)) {
                        const targetSelect = document.getElementById(`component_${categoryId}`);
                        if (!targetSelect) continue;
        
                        const incompatibleSet = new Set(incompatibleIds.map(id => id.toString()));
                        const currentValue = targetSelect.value;
        
                        Array.from(targetSelect.options).forEach(option => {
                            if (option.value === "") return;
        
                            if (incompatibleSet.has(option.value)) {
                                option.disabled = true;
        
                                if (currentValue === option.value) {
                                    targetSelect.value = "";
                                }
                            }
                        });
                    }
                });
            }
        });
        </script>
        
    
    
@endsection
