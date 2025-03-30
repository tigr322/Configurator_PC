<?php

namespace App\Http\Controllers\Components;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Component;
use App\Models\CompatibilityRule;
class ComponentController extends Controller
{
 /**ы
     * Показать список всех комплектующих с фильтрацией и сортировкой
     */
    public function index(Request $request)
    {
        $query = Component::query()->with('category');

        // Фильтрация
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('brand')) {
            $query->where('brand', 'like', '%' . $request->brand . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('pagination')) {
            $perPage = (int) $request->input('pagination');
            $components = $query->paginate($perPage);
        } else {
            $components = $query->paginate(5); // или любое значение по умолчанию
        }
        // Сортировка
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        // Пагинация
      

        // Все категории для фильтра
        $categories = Category::all();

        return view('pccomponents.catalog', compact('components', 'categories'));
    }

    /**
     * Показать один компонент
     */
    public function checkCompatibility(Request $request)
    {
        $component = Component::findOrFail($request->component_id);
        $sourceCategoryId = $request->category_id;
    
        // Получаем все правила, где выбрана категория — source
        $rules = CompatibilityRule::where('category1_id', $sourceCategoryId)->get();
    
        $result = [];
    
        foreach ($rules as $rule) {
            $targetCategoryId = $rule->category2_id;
            $conditions = $rule->condition; // это JSON поле, уже приведенное к массиву (если каст настроен)
    
            // Получаем все компоненты из категории назначения
            $compatible = Component::where('category_id', $targetCategoryId)->get()->filter(function ($targetComponent) use ($component, $conditions) {
                $sourceData = json_decode($component->compatibility_data, true);
                $targetData = json_decode($targetComponent->compatibility_data, true);
    
                foreach ($conditions as $field => $operator) {
                    $sourceValue = $sourceData[$field] ?? null;
                    $targetValue = $targetData[$field] ?? null;
    
                    if (is_null($sourceValue) || is_null($targetValue)) return false;
    
                    switch ($operator) {
                        case '==': if ($sourceValue != $targetValue) return false; break;
                        case '>=': if ($sourceValue < $targetValue) return false; break;
                        case '<=': if ($sourceValue > $targetValue) return false; break;
                        case '>':  if ($sourceValue <= $targetValue) return false; break;
                        case '<':  if ($sourceValue >= $targetValue) return false; break;
                        default: return false;
                    }
                }
    
                return true;
            })->values(); // Сброс ключей массива
    
            $result[$targetCategoryId] = $compatible;
        }
    
        return response()->json($result);
    }
    public function checkCompatibilityMulti(Request $request)
{
    $selectedComponents = $request->input('selected_components', []);

    // Загружаем все выбранные компоненты
    $components = Component::whereIn('id', array_values($selectedComponents))
        ->get()
        ->keyBy('id');

    $result = [];

    // Получаем все правила совместимости
    $rules = CompatibilityRule::all();

    foreach ($rules as $rule) {
        $sourceCategoryId = $rule->category1_id;
        $targetCategoryId = $rule->category2_id;
        $conditions = $rule->condition;

        // Проверяем, выбран ли компонент из sourceCategory
        if (!isset($selectedComponents[$sourceCategoryId])) {
            continue;
        }

        $sourceComponentId = $selectedComponents[$sourceCategoryId];
        $sourceComponent = $components[$sourceComponentId] ?? null;

        if (!$sourceComponent) continue;

        $sourceData = json_decode($sourceComponent->compatibility_data, true);

        // Получаем все компоненты из целевой категории
        $targetComponents = Component::where('category_id', $targetCategoryId)->get();

        foreach ($targetComponents as $targetComponent) {
            $targetData = json_decode($targetComponent->compatibility_data, true);

            $isCompatible = true;

            foreach ($conditions as $field => $operator) {
                $sourceValue = $sourceData[$field] ?? null;
                $targetValue = $targetData[$field] ?? null;

                if (is_null($sourceValue) || is_null($targetValue)) {
                    $isCompatible = false;
                    break;
                }

                switch ($operator) {
                    case '==': $isCompatible = $sourceValue == $targetValue; break;
                    case '>=': $isCompatible = $sourceValue >= $targetValue; break;
                    case '<=': $isCompatible = $sourceValue <= $targetValue; break;
                    case '>':  $isCompatible = $sourceValue >  $targetValue; break;
                    case '<':  $isCompatible = $sourceValue <  $targetValue; break;
                    default:   $isCompatible = false; break;
                }

                if (!$isCompatible) break;
            }

            // Если не совместимо — добавим в "исключённые" компоненты
            if (!$isCompatible) {
                $result[$targetCategoryId][] = $targetComponent->id;
            }
        }
    }

    return response()->json($result);
}

    public function show($id)
    {
        $component = Component::with(['category', 'parsedData'])->findOrFail($id);

        return view('pccomponents.show', compact('component'));
    }
}
