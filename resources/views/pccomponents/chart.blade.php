
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Каталог комплектующих</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.png') }}">
</head>

<body>

    @include('layouts.navigation')

<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-4">График цены: {{ $component->name }}</h2>
    <canvas id="priceChart" height="100"></canvas>
    <a href="{{ route('components.show', $component->id) }}"
       class="mt-6 inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
        Назад
    </a>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('priceChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($labels) !!},
        datasets: [{
            label: 'Цена (₽)',
            data: {!! json_encode($prices) !!},
            borderWidth: 2,
            fill: false,
            tension: 0.4,
            pointRadius: 3
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: false,
                ticks: {
                    callback: function(value) {
                        return value + ' ₽';
                    }
                }
            }
        }
    }
});
</script>
</body>