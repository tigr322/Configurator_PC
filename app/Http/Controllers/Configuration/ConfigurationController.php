<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Configurations;
use App\Models\Category;
use App\Models\Component;
use App\Models\CompatibilityRule;
class ConfigurationController extends Controller
{
    private static function compareValues($val1, $val2, $operator)
{
    switch ($operator) {
        case '==': return $val1 == $val2;
        case '!=': return $val1 != $val2;
        case '>': return $val1 > $val2;
        case '<': return $val1 < $val2;
        case '>=': return $val1 >= $val2;
        case '<=': return $val1 <= $val2;
        default: return false;
    }
}

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
/*
public function checkCompatibility(Request $request)
{
    $component = Component::findOrFail($request->component_id);
    $sourceCategoryId = $request->category_id;

    // Ищем правила, где выбрана текущая категория как category1
    $rules = CompatibilityRule::where('category1_id', $sourceCategoryId)->get();

    $result = [];

    foreach ($rules as $rule) {
        $condition = $rule->condition;
        $targetCategoryId = $rule->category2_id;

        $compatibleComponents = Component::where('category_id', $targetCategoryId)->get()->filter(function ($targetComponent) use ($component, $condition) {
            foreach ($condition as $key => $operator) {
                $val1 = $component->compatibility_data[$key] ?? null;
                $val2 = $targetComponent->compatibility_data[$key] ?? null;

                if (is_null($val1) || is_null($val2)) return false;

                switch ($operator) {
                    case '==': if ($val1 != $val2) return false; break;
                    case '>=': if ($val1 < $val2) return false; break;
                    case '<=': if ($val1 > $val2) return false; break;
                    default: return false;
                }
            }
            return true;
        })->values(); // to reset keys

        $result['compatible'][$targetCategoryId] = $compatibleComponents;
    }

    return response()->json($result);
}

*/
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'components' => 'array',
    ]);

    $selectedComponents = Component::whereIn('id', $request->components)->get();

    // Проверка совместимости компонентов
    foreach ($selectedComponents as $component1) {
        foreach ($selectedComponents as $component2) {
            if ($component1->id === $component2->id) continue;

            // Получаем правило между категориями
            $rule = CompatibilityRule::where(function ($q) use ($component1, $component2) {
                $q->where('category1_id', $component1->category_id)
                  ->where('category2_id', $component2->category_id);
            })->orWhere(function ($q) use ($component1, $component2) {
                $q->where('category1_id', $component2->category_id)
                  ->where('category2_id', $component1->category_id);
            })->first();

            if ($rule) {
                $condition = json_decode($rule->condition, true);
                $data1 = json_decode($component1->compatibility_data, true);
                $data2 = json_decode($component2->compatibility_data, true);

                foreach ($condition as $key => $operator) {
                    if (!isset($data1[$key]) || !isset($data2[$key])) {
                        return back()->withErrors("Параметр '$key' не найден в одном из компонентов.");
                    }

                    $value1 = $data1[$key];
                    $value2 = $data2[$key];

                    if (!self::compareValues($value1, $value2, $operator)) {
                        return back()->withErrors("Несовместимые компоненты: '{$component1->name}' и '{$component2->name}' по параметру '$key'.");
                    }
                }
            }
        }
    }

    // Создаем конфигурацию
    $config = Configurations::create([
        'user_id' => Auth::id(),
        'name' => $request->name,
        'total_price' => 0,
    ]);

    $total = 0;
    foreach ($selectedComponents as $component) {
        $config->components()->attach($component->id);
        $total += $component->price;
    }

    $config->total_price = $total;
    $config->save();

    return redirect()->route('configurator')->with('success', 'Конфигурация успешно создана!');
}

    
}
