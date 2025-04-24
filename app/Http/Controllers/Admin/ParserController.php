<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RoachPHP\Spider\Configuration\Overrides;
use Illuminate\Support\Facades\Artisan;
use App\Spiders\ComponentSpider;
use App\Spiders\ComponentRegardSpider;
use RoachPHP\Roach;
use App\Jobs\ParseMarketJob;
use App\Models\Category;
use App\Models\Component;
use App\Models\Markets;
use App\Models\MarketsUrls;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Downloader\Middleware\DelayRequestsMiddleware;
class ParserController extends Controller
{
    public function parse(Request $request)
{
    $validated = $request->validate([
        'category_id' => 'required|integer|exists:categories,id',
        'market_id' => 'required|integer|exists:markets,id',
    ]);
    ParseMarketJob::dispatch($validated)
    ->onQueue('parsing');
    // Get the URL from MarketsUrls
    $marketUrl = MarketsUrls::where('category_id', $validated['category_id'])
        ->where('market_id', $validated['market_id'])
        ->first();

    if (!$marketUrl) {
        return redirect()->back()
            ->with('error', 'URL для выбранной категории и магазина не найден');
    }

    Log::info('Starting spider with params:', $validated);
//dd($marketUrl->url);
    if ($validated['market_id'] == 1) { 
        Roach::startSpider(
            ComponentSpider::class,
            new Overrides(
                startUrls: [$marketUrl->url], 
            ),
            context: [
                'category_id' => $validated['category_id'],
                'market_id' => $validated['market_id'],
            ]
        );
    } elseif ($validated['market_id'] == 2) {
        Roach::startSpider(
            ComponentRegardSpider::class,
            new Overrides(
                startUrls: [$marketUrl->url],
                downloaderMiddleware: [
                   
                    RequestDeduplicationMiddleware::class,
                ], // Access the url property from the model
            ),

            context: [
                'category_id' => $validated['category_id'],
                'market_id' => $validated['market_id'],
            ]
        );
    }

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
            'market_id' => 'required',
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
        }else{
            $foto = 'images/defaulte_image.jpg';    
        }
    
        Component::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['component_name'],
            'market_id' =>$validated['market_id'],
            'brand' => $validated['component_brand'],
            'price'=>  $validated['component_price'],
            'shop_url' => $validated['component_market_url'],
            'compatibility_data' => $validated['compatibility_data'] ?? null,
            'image_url' => $foto,
            'characteristics' =>$validated['charastiristics'],
        ]);
    
        return redirect()->back()->with('success', 'Компонент успешно добавлен!');
    }
    public function addMarket(Request $request){
        $validated = $request->validate([
            'market_name' => 'required|string|max:32',
        ]);
        Markets::create(['name' => $validated['market_name']]);

        return redirect()->back()
        ->with('success', 'Успешно добавлен новый магазин!');
    }
    
}
