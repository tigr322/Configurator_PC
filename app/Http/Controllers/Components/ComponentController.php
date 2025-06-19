<?php

namespace App\Http\Controllers\Components;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Component;
use App\Models\Markets;
use App\Models\CompatibilityRule;
use App\Models\MarketsUrls;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class ComponentController extends Controller
{

    public function index(Request $request)
    {   
        $validated = $request->validate([
            'socket' => 'nullable|string',
            'memory_type' => 'nullable|string',
            'manufacturer' => 'nullable|string', 
        ]);
        
        
        $query = Component::query()->with('category')->with('markets');
    
       
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }
        
        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }
        
        if ($request->filled('brand')) {
            $query->where('brand', 'like', '%'.$request->brand.'%');
        }
        
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
       
        if ($request->socket) {
            $query->whereJsonContains('compatibility_data->socket', $request->socket);
        }
        
        if ($request->manufacturer) {
            $query->where('brand', $request->manufacturer);
        }
        
        if ($request->memory_type) {
            $query->whereJsonContains('compatibility_data->memory_type', $request->memory_type);
        }
        if ($request->market) {
            $query->where('market_id', $request->market);
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
    
       
        $perPage = $request->filled('pagination') ? (int)$request->pagination : 12;
        $components = $query->paginate($perPage);
        $view = $request->input('view', 'grid');
        
$components = Component::where('market_id', 4)->get();

foreach ($components as $component) {
    $words = explode(' ', $component->name);

    if (isset($words[1])) {
        $component->brand = $words[1];
        $component->save();
    }
}
        $categories = Category::all();
        $rules = CompatibilityRule::all();
        $markets = Markets::all();
        $marketsUrls = MarketsUrls::all();
        if ($request->ajax()) {
            return view('pccomponents.partial.components', [
                'components' => $components,
                'view' => $view,
                'categories' => $categories,
                'rules' => $rules,
                'markets' => $markets,
                'marketsUrls' => $marketsUrls,
            ])->render();
        }
    
        return view('pccomponents.catalog', compact('components', 'categories', 'rules', 'markets', 'marketsUrls'));
    }
    public function getUrlsByMarket(Request $request)
    {
        $marketId = $request->input('market_id');
        $urls = MarketsUrls::getByMarket($marketId);
        
        return response()->json($urls);
    }
    
    public function checkCompatibilityMulti(Request $request)
    {
        $selectedComponents = collect($request->input('selected_components', []))
            ->filter(fn($value) => is_numeric($value) && $value > 0)
            ->map(fn($value) => (int)$value)
            ->toArray();
    
       
        $componentIds = array_values($selectedComponents);
    
       
        $validator = Validator::make($request->all(), [
            'selected_components' => 'required|array|min:1',  
            'selected_components.*' => 'integer|exists:components,id',  
        ]);
    
       
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $components = Component::whereIn('id', $componentIds)
            ->get()
            ->keyBy('id');
    
        $result = [];
        $rules = CompatibilityRule::all();
    
        foreach ($rules as $rule) {
            $sourceCategoryId = $rule->category1_id;
            $targetCategoryId = $rule->category2_id;
            $conditions = $rule->condition;
    
            if (!isset($selectedComponents[$sourceCategoryId])) {
                continue;
            }
    
            $sourceComponentId = $selectedComponents[$sourceCategoryId];
            $sourceComponent = $components[$sourceComponentId] ?? null;
    
            if (!$sourceComponent) continue;
    
            $sourceData = json_decode($sourceComponent->compatibility_data, true);
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
    
                    $sourceArray = $this->normalizeToArray($sourceValue);
                    $targetArray = $this->normalizeToArray($targetValue);
    
                    if (in_array($field, ['form_factor', 'interface'])) {
                        $isCompatible = count(array_intersect($sourceArray, $targetArray)) > 0;
                        if (!$isCompatible) break;
                        continue;
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
    
                if (!$isCompatible) {
                    $result[$targetCategoryId][] = $targetComponent->id;
                }
            }
        }
    
        return response()->json($result);
    }
     
protected function normalizeToArray($value): array
{
    if (is_array($value)) {
        return $value;
    }
    
    if (is_string($value) && str_contains($value, ',')) {
        return array_map('trim', explode(',', $value));
    }
    
    return [$value];
}
     public function delete($id) 
     {
         try {
             $component = Component::findOrFail($id);
     
            
             if ($component->image_url) {
                 $imageName = basename($component->image_url);
                 $storagePath = 'public/products/' . $imageName;
     
                 if (Storage::exists($storagePath)) {
                     Storage::delete($storagePath);
                 } else {
                   
                     Log::warning("Файл изображения не найден: " . $storagePath);
                 }
             }
     
             $component->delete();
     
             return response()->json([
                 'success' => true,
                 'message' => 'Компонент успешно удален'
             ]);
     
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при удалении компонента: ' . $e->getMessage()
                ], 500);
            }
        
     }

public function saveRules(Request $request)
{
    $rules = $request->input('rules', []);
    $deletedRules = $request->input('deleted_rules') ? json_decode($request->input('deleted_rules')) : [];

    
    if (!empty($deletedRules)) {
        CompatibilityRule::whereIn('id', $deletedRules)->delete();
    }

    foreach ($rules as $rule) {
        if (!isset($rule['category1_id'], $rule['category2_id'], $rule['condition'])) {
            continue; 
        }

        CompatibilityRule::updateOrCreate(
            ['id' => $rule['id'] ?? null], 
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
        'market_id' => 'required',
        'shop_url' => 'nullable|url',
        'category_id' => 'required',
        'compatibility_data' => 'nullable|json',
        'characteristics' => 'nullable|string|max:512',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $updateData = [
        'name' => $request->name,
        'brand' => $request->brand,
        'category_id' => $request->category_id,
        'price' => $request->price,
        'market_id' => $request->market_id,
        'shop_url' => $request->shop_url,
        'compatibility_data' => $request->compatibility_data,
        'characteristics' => $request->characteristics,
    ];

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $foto = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $filename = 'products/' . $foto;
    
        $stream = fopen($image->getRealPath(), 'r+');
        Storage::disk('public')->put($filename, $stream);
        fclose($stream);
    
        $updateData['image_url'] = $foto;
    }
    
    
    

    $component->update($updateData);

    return redirect()->back()->with('success', 'Компонент обновлён успешно.');
}

public function getUrls($marketId)
{
    $marketUrls = MarketsUrls::where('market_id', $marketId)->with('category')->get();
    $categories = Category::all();

    return response()->json([
        'urls' => $marketUrls,
        'categories' => $categories,
    ]);
}

    public function show($id)
    {
        $component = Component::with(['category', 'parsedData'])->findOrFail($id);
        $categories = Category::all(); 

        $markets = Markets::all();
       
        return view('pccomponents.show', compact('component','categories','markets'));
    }
    public function chart(Component $component)
    {
        $parsedPrices = $component->parsedData()
            ->orderBy('created_at')
            ->get(['price', 'created_at']);
    
        $labels = $parsedPrices->pluck('created_at')->map(fn($dt) => $dt->format('d.m H:i'));
        $prices = $parsedPrices->pluck('price');
    
        return view('pccomponents.chart', [
            'component' => $component,
            'labels' => $labels,
            'prices' => $prices,
        ]);
    }
}
