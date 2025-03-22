@extends('layouts.navigation')  
@section('title', 'Конфигурация: ' . $configuration->name)

@section('content')
    <div class="container">
        <h1>{{ $configuration->name }}</h1>
        <p>Итоговая стоимость: {{ number_format($configuration->total_price, 2) }} ₽</p>

        <h3>Список комплектующих:</h3>
        <ul>
            @foreach($configuration->components as $component)
                <li>
                    <strong>{{ $component->category->name }}:</strong> 
                    {{ $component->name }} — {{ number_format($component->price, 2) }} ₽
                </li>
            @endforeach
        </ul>
    </div>
@endsection
