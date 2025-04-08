<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use RoachPHP\Spider\Configuration\Overrides;
use Illuminate\Support\Facades\Artisan;
use App\Spiders\ComponentSpider;
use RoachPHP\Roach;

use Illuminate\Support\Facades\Log;
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
}
