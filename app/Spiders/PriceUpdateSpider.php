<?php

namespace App\Spiders;

use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use App\Models\Component;
use App\Models\ParsedData;

class PriceUpdateSpider extends BasicSpider
{
    public array $startUrls = []; // получаем из Overrides

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        [UserAgentMiddleware::class, [
            "userAgent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36"
        ]],
    ];

    public function parse(Response $response): \Generator
    {
        $component = Component::find($this->context['component_id']);
    
        if (!isset($this->context['component_id']) || !$component) {
            yield ParseResult::item([]);
            return;
        }
    
        // Попробуем сначала data-meta-price
        $priceNode = $response->filter('[data-meta-price]')->first();
    
        // Если нет, пробуем другой способ (как на новых страницах)
       
        // Если всё ещё нет — считаем цену не найденной
        if ($priceNode->count() === 0) {
            logger()->warning("⛔ Не найдена цена у компонента {$component->name} [ID {$component->id}]");
    
            ParsedData::create([
                'component_id' => $component->id,
                'source' => 'citilink',
                'availability' => 0,
            ]);
    
            yield ParseResult::item([]);
            return;
        }
    
        // Парсим текст цены
        $priceText = $priceNode->attr('data-meta-price') ?? $priceNode->text();
        $price = (int) preg_replace('/\D+/', '', $priceText);
    
        ParsedData::create([
            'component_id' => $component->id,
            'source' => 'citilink',
            'price' => $price,
            'availability' => 1,
        ]);
        $component->update(['price' => $price]);
    
        logger()->info("✅ Обновлена цена: {$component->name} — {$price} ₽");
    
        yield ParseResult::item([]);
    }
}