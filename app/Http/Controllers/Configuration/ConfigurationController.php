<?php

namespace App\Http\Controllers\Configuration;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Configurations;
use App\Models\Category;
use App\Models\User;
use App\Models\ConfigurationVote;
use App\Models\Comment;
use App\Models\Component;
class ConfigurationController extends Controller
{
    public function configurations(Request $request)
{
   
    $query = Configurations::query()
        ->withCount([
            'votes as likes_count' => function ($query) {
                $query->where('is_like', true);
            },
            'votes as dislikes_count' => function ($query) {
                $query->where('is_like', false);
            }
        ]);
    if ($request->filled('like')) {
        if ($request->like === 'like') {
            $query->orderByDesc('likes_count');
        } elseif ($request->like === 'dislike') {
            $query->orderByDesc('dislikes_count');
        }
        else{
            $query->orderByDesc('likes_count');
        }
    }
   
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }
    
   
    if ($request->filled('component')) {
        $query->whereHas('components', function($query) use ($request) {
            $query->where('name', 'like', '%' . $request->component . '%');
        });
    }
    if ($request->filled('pagination')) {
        $perPage = (int) $request->input('pagination');
        $col = $query->paginate($perPage);
    } else {
        $col = $query->paginate(5);
    }
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
    
    
    
    $builds = $query->get();  
    
    return view('configurationbuild.builds', compact('builds', 'col'));
}

public function toggleMode(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация']);
        }

       
        $currentMode = session('configurator_mode', false);
        
        session(['configurator_mode' => !$currentMode]);
        
       
        // Auth::user()->update(['configurator_mode' => !$currentMode]);
        
        return response()->json([
            'success' => true,
            'new_mode' => !$currentMode,
            'message' => 'Режим конфигуратора ' . ($currentMode ? 'выключен' : 'включен')
        ]);
    }
    public function comments(Request $request)
    {
        $request->validate([
            'configuration_id' => 'required|exists:configurations,id',
            'body' => 'required|string',
        ]);
        $user = Auth::user();
        $userId = $user->id;
        Comment::create([
            'configuration_id' => $request->configuration_id,
            'user_id' => $userId,
            'body' => $request->body,
        ]);

        return back();
    }
    public function destroyComments(Comment $comment)
    {
        $user = Auth::user();
        $userId = $user->id;
        // Проверка: только автор или админ может удалить комментарий
        if ($userId !== $comment->user_id && (User::where("id",$userId)->admin)!=1) {
            abort(403);
        }
    
        $comment->delete();
    
        return back()->with('success', 'Комментарий удалён!');
    }
    
// В ConfigurationsController.php
public function getComponentsHtml($id)
{
    $build = Configurations::with('components.category')->findOrFail($id);

    return view('configurationbuild.components_list', compact('build'));
}

/*
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
    
        return view('configurationbuild.showconf', compact('configuration')); 
    }
    public function publicShow($id){
        $build = Configurations::with('components.category')->findOrFail($id);
        return view('configurationbuild.publicBuild', compact('build'));
    }
    public function create()
    {
        $categories = Category::with('components')->get();
        //return view('configurationbuild.configurator', compact('categories'));
        return view('configurationbuild.configurator', compact('categories'));
    }
   
    //голосование
    public function like(Configurations $configuration)
    {
        $this->vote($configuration, true, null);
    
        return response()->json([
            'build_id' => $configuration->id,
            'likes' => $configuration->likes()->count(),
            'dislikes' => $configuration->dislikes()->count(),
            'best_votes' => $configuration->bestBuildVotes()->count(),
        ]);
    }

    public function dislike(Configurations $configuration)
    {
        $this->vote($configuration, false, null);
        return response()->json([
            'build_id' => $configuration->id,
            'likes' => $configuration->likes()->count(),
            'dislikes' => $configuration->dislikes()->count(),
            'best_votes' => $configuration->bestBuildVotes()->count(),
        ]);
    }

    public function bestBuild(Configurations $configuration)
    {
        $this->vote($configuration, null, true);
        return response()->json([
            'build_id' => $configuration->id,
            'likes' => $configuration->likes()->count(),
            'dislikes' => $configuration->dislikes()->count(),
            'best_votes' => $configuration->bestBuildVotes()->count(),
        ]);
    }

    private function vote(Configurations $configuration, $isLike, $isBestBuildVote)
    {
        $user = Auth::user();
        $userid = $user->id;
        ConfigurationVote::updateOrCreate(
            [
                'user_id' =>  $userid,
                'configuration_id' => $configuration->id,
            ],
            [
                'is_like' => $isLike,
                'is_best_build_vote' => $isBestBuildVote,
            ]
        );
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
