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
                                {{ $component->name }} ({{ number_format($component->price, 2) }} ₽)
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
@endsection
