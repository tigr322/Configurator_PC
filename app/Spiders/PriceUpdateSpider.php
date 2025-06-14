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
    public array $startUrls = [];

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

        // Сохраняем HTML для отладки
        file_put_contents(storage_path("logs/html_kns_{$component->id}.html"), $response->getBody());

        // Ищем цену
        $priceNode = $response->filter('.price-info .price-val')->first();

        if ($priceNode->count() === 0) {
            logger()->warning("⛔ KNS: Не найдена цена у компонента {$component->name} [ID {$component->id}]");

            ParsedData::create([
                'component_id' => $component->id,
                'source' => 'kns',
                'availability' => 0,
            ]);

            yield ParseResult::item([]);
            return;
        }

        // Получаем текст цены и чистим
        $priceText = $priceNode->text();
        $price = (int) preg_replace('/\D+/', '', $priceText);

        ParsedData::create([
            'component_id' => $component->id,
            'source' => 'kns',
            'price' => $price,
            'availability' => 1,
        ]);

        $component->update(['price' => $price]);

        logger()->info("✅ KNS: Обновлена цена — {$component->name} — {$price} ₽");

        yield ParseResult::item([]);
    }
}