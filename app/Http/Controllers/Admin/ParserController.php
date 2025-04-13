<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RoachPHP\Spider\Configuration\Overrides;
use Illuminate\Support\Facades\Artisan;
use App\Spiders\ComponentSpider;
use RoachPHP\Roach;
use App\Models\Category;
use App\Models\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class ParserController extends Controller
{
    public function parse(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'source_url' => 'required|url',
        ]);

        Log::info('Starting spider with params:', $validated);

        // Правильный способ запуска через Roach
        Log::info('Before startSpider');
        Roach::startSpider(
            ComponentSpider::class,
            new Overrides(
                startUrls: [$validated['source_url']],
            ),
            context: [
                'category_id' => $validated['category_id'],
            ]
        );
        
        Log::info('After startSpider');

        return redirect()->back()
            ->with('success', 'Парсинг успешно запущен!');
    }
    public function addCategory(Request $request){
        $validated = $request->validate([
            'category_name' => 'required|string|max:32',
        ]);
        Category::create(['name' => $validated['category_name']]);

        return redirect()->back()
        ->with('success', 'Успешно добавлена категория товара!');
    }

    
    public function addComponent(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer',
            'component_name' => 'required|string|max:128',
            'component_price'=>  'required|numeric',
            'component_brand' => 'required|string|max:32',
            'component_market_url' => 'required|url',
            'compatibility_data' => 'required|json',
            'component_image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'charastiristics'=> 'nullable|string|max:512'
        ]);
    
        $imagePath = null;
    
        if ($request->hasFile('component_image')) {
            $image = $request->file('component_image');
            $foto = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $filename = 'products/' . $foto;
    
            // Сохраняем файл в disk('public') => storage/app/public/products/
            Storage::disk('public')->putFileAs('products', $image, $foto);
    
            // Сохраняем путь для доступа
            $imagePath = $filename;
        }
    
        Component::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['component_name'],
            'brand' => $validated['component_brand'],
            'price'=>  $validated['component_price'],
            'shop_url' => $validated['component_market_url'],
            'compatibility_data' => $validated['compatibility_data'] ?? null,
            'image_url' => $foto,
            'characteristics' =>$validated['charastiristics'],
        ]);
    
        return redirect()->back()->with('success', 'Компонент успешно добавлен!');
    }
    
}
