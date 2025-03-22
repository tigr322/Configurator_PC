@extends('layouts.navigation')  
@section('title', 'Конфигурации')

@section('content')
    <div class="container">
        <h1>Мои конфигурации</h1>
        <label for="build-select">Выберите конфигурацию:</label>
        <select id="build-select" class="build-dropdown" onchange="selectBuild(this)">
            <option value="">-- Выберите конфигурацию --</option>
            @foreach($builds as $build)
                <option value="{{ $build->id }}">{{ $build->name }}</option>
            @endforeach
        </select>
    </div>

    <script>
        function selectBuild(selectElement) {
            const selectedId = selectElement.value;
            if (selectedId) {
                window.location.href = "/configurations/" + selectedId;
            }
        }
    </script>
@endsection
