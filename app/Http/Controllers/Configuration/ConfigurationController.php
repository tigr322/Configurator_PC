<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Configurations;
use App\Models\Category;
use App\Models\Component;
class ConfigurationController extends Controller
{
    public function configurations(Request $request){
        $builds = Configurations::all();  // Получаем все конфигурации

        return view('configurationbuild.builds', ['builds' => $builds]);  // Передаем данные в представление
    }
    public function show($id)
    {
        $configuration = Configurations::with('components.category')->findOrFail($id);
    
        return view('configurationbuild.showconf', compact('configuration')); // Убедитесь, что переменная правильно названа
    }
    
    public function create()
{
    // Загружаем все категории и их компоненты pc-configurator/resources/views/configurationbuild/configurator.blade.php
    $categories = Category::with('components')->get();
//return view('configurationbuild.configurator', compact('categories'));
    return view('configurationbuild.configurator', compact('categories'));
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'components' => 'array',
    ]);

    // Создаем конфигурацию
    $config = Configurations::create([
        'user_id' => Auth::id(),
        'name' => $request->name,
        'total_price' => 0,
    ]);

    $total = 0;

    // Привязываем компоненты
    foreach ($request->components as $componentId) {
        if ($componentId) {
            $config->components()->attach($componentId);
            $component = Component::find($componentId);
            $total += $component->price;
        }
    }

    // Обновляем итоговую цену
    $config->total_price = $total;
    $config->save();

    return redirect()->route('configurator')->with('success', 'Конфигурация успешно создана!');
}
    
}
