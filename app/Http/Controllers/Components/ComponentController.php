<?php

namespace App\Http\Controllers\Components;
use Illuminate\Support\Facades\Validator;
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
            $components = $query->paginate(5); 
        }
       
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
        $rules = CompatibilityRule::all();

        return view('pccomponents.catalog', compact('components', 'categories', 'rules'));
    }

    /**
     * Показать один компонент
     */
  
    public function checkCompatibilityMulti(Request $request)
{
    $data = [
    'selected_components' => $request->input('selected_components'),
];

$valided = Validator::make($data, [
    'selected_components' => 'required|array|min:1',
    'selected_components.*' => 'required|integer|exists:components,id',
]);

if ($valided->fails()) {
    return response()->json($valided->errors(), 422);
}

    
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
public function delete($id)
{
    $component = Component::find($id);

    if ($component) {
        $component->delete();
        return redirect()->back()->with('success', 'Компонент успешно удалён.');
    }

    return redirect()->back()->withErrors(['Компонент не найден.']);
}


public function saveRules(Request $request)
{
    $rules = $request->input('rules', []);
    $deletedRules = $request->input('deleted_rules') ? json_decode($request->input('deleted_rules')) : [];

    // Удаление правил
    if (!empty($deletedRules)) {
        CompatibilityRule::whereIn('id', $deletedRules)->delete();
    }

    // Сохранение/обновление
    foreach ($rules as $rule) {
        if (!isset($rule['category1_id'], $rule['category2_id'], $rule['condition'])) {
            continue; // Пропускаем если данных не хватает
        }

        CompatibilityRule::updateOrCreate(
            ['id' => $rule['id'] ?? null], // может быть null
            [
                'category1_id' => $rule['category1_id'],
                'category2_id' => $rule['category2_id'],
                'condition' => json_decode($rule['condition'], true),
            ]
        );
    }

    return redirect()->back()->with('success', 'Правила сохранены');
}


public function update(Request $request, $id)
{
    $component = Component::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'brand' => 'nullable|string|max:255',
        'price' => 'required|numeric',
        'shop_url' => 'nullable|url',
        'category_id' => 'required',
        'compatibility_data' => 'nullable|json',
    ]);

    $component->update([
        'name' => $request->name,
        'brand' => $request->brand,
        'category_id' =>  $request->category_id, 
        'price' => $request->price,
        'shop_url' => $request->shop_url,
        'compatibility_data' => $request->compatibility_data,
    ]);

    return redirect()->back()->with('success', 'Компонент обновлён успешно.');
}


    public function show($id)
    {
        $component = Component::with(['category', 'parsedData'])->findOrFail($id);
        $categories = Category::all(); 
        return view('pccomponents.show', compact('component','categories'));
    }
}
