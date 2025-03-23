@extends('layouts.navigation')  
@section('title', 'Создать конфигурацию')

@section('content')
    <div class="container">
        <h1>Создание конфигурации ПК</h1>

        <form action="{{ route('configurations.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label  for="config-name">Название конфигурации:</label>
               
                <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;"  type="text" name="name" id="config-name" class="form-control" required>
            </div>

            <h3>Выберите комплектующие:</h3>

            @foreach($categories as $category)
                <div class="mb-3">
                    <label for="component_{{ $category->id }}">{{ $category->name }}:</label>
                    <select style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" name="components[{{ $category->id }}]" id="component_{{ $category->id }}" class="form-control">
                        <option value="">-- Не выбрано --</option>
                        @foreach($category->components as $component)
                            <option value="{{ $component->id }}">
                                {{ $component->name }} ({{ number_format($component->price, 2) }} $)
                            </option>
                        @endforeach
                    </select>
                </div>
            @endforeach

            <button type="submit" class="inline-block px-5 py-1.5 border border-transparent hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                Создать конфигурацию
            </button>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selects = document.querySelectorAll('select[id^="component_"]');
    
            selects.forEach(select => {
                select.addEventListener('change', function () {
                    const componentId = this.value;
                    const categoryId = this.id.replace('component_', '');
    
                    if (!componentId) return;
    
                    fetch('/configurator/check-compatibility', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            component_id: componentId,
                            category_id: categoryId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        for (const [targetCategoryId, components] of Object.entries(data)) {
                            const targetSelect = document.getElementById(`component_${targetCategoryId}`);
                            if (!targetSelect) continue;
    
                            // Сохраняем текущий выбор (если есть)
                            const currentValue = targetSelect.value;
    
                            // Сброс списка
                            targetSelect.innerHTML = '<option value="">-- Не выбрано --</option>';
    
                            components.forEach(c => {
                                const option = document.createElement('option');
                                option.value = c.id;
                                option.textContent = `${c.name} (${parseFloat(c.price).toFixed(2)} $)`;
                                targetSelect.appendChild(option);
                            });
    
                            // Если предыдущий выбор всё ещё есть в новом списке — восстановим его
                            if ([...targetSelect.options].some(opt => opt.value === currentValue)) {
                                targetSelect.value = currentValue;
                            }
                        }
                    });
                });
            });
        });
    </script>
    
    
@endsection
