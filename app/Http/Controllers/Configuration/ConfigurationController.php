<?php

namespace App\Http\Controllers\Configuration;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Configurations;
use App\Models\Category;
use App\Models\User;
use App\Models\Component;
class ConfigurationController extends Controller
{
    public function configurations(Request $request)
{
    // Начинаем запрос с моделей Configurations и их компонентов
    $query = Configurations::query()->with('components');
    
    // Фильтрация по имени конфигурации
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }
    
    // Фильтрация по компонентам
    if ($request->filled('component')) {
        $query->whereHas('components', function($query) use ($request) {
            $query->where('name', 'like', '%' . $request->component . '%');
        });
    }
    if ($request->filled('pagination')) {
        $perPage = (int) $request->input('pagination');
        $col = $query->paginate($perPage);
    } else {
        $col = $query->paginate(5); // или любое значение по умолчанию
    }
    
    // Сортировка по цене pagination
    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('total_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('total_price', 'desc');
                break;
            default:
                $query->latest();
        }
    } else {
        $query->latest();
    }
   
    // Получаем все конфигурации после применения фильтров и сортировок
    $builds = $query->get();  // Используем get() вместо all(), чтобы получить только отфильтрованные данные
    
    // Передаем конфигурации в представление
    return view('configurationbuild.builds', compact('builds', 'col'));
}
/*public function edit($id)
{
    $build = Configurations::with('components.category')->findOrFail($id);
    return view('builds.edit', compact('build'));
}

public function update(Request $request, $id)
{
    $build = Configurations::findOrFail($id);
    $build->update($request->only(['name']));
    return redirect()->route('builds.index')->with('success', 'Конфигурация обновлена');
}
*/
public function destroy($id)
{
    $build = Configurations::findOrFail($id);
    $build->delete();
    return redirect()->back()->with('success', 'Конфигурация удалена');
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
    public function publicShow($id){
        $build = Configurations::with('components.category')->findOrFail($id);
        return view('configurationbuild.publicBuild', compact('build'));
    }
    //создание конфигурации и загрузки ее в бд
    public function store(Request $request)
    {  
        $user = Auth::user();
        $userid = $user->id;
        if ($user->admin == 0 && Configurations::where("user_id",$userid)->count() >= 5) {
            return redirect()->route('configurator')->with('error', 'Ограничение по количеству конфигурации');
        }
        $request->merge([
            'components' => array_values($request->input('components', [])),
        ]);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'components' => 'required|array|min:1',
            'components.*' => 'required|integer|exists:components,id',
        ], [
            'components.required' => 'Нужно выбрать хотя бы один компонент.',
            'components.*.exists' => 'Один или несколько выбранных компонентов не существуют.',
            'components.*.required' => 'Выберите компонент для категории :attribute.',

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
