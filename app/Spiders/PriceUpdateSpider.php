<?php

namespace App\Spiders;

use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use App\Models\Component;
use App\Models\ParsedData;
use RoachPHP\Downloader\Middleware\DelayMiddleware;
class PriceUpdateSpider extends BasicSpider
{
    public array $startUrls = [];

   

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        [UserAgentMiddleware::class, [
            "userAgent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36"
        ]],
       
    ];

    public function parse(Response $response): \Generator
    {
       
        $component = Component::find($this->context['component_id']);

        if (!isset($this->context['component_id']) || !$component) {
            yield ParseResult::item([]);
            return;
        }

        $marketId = $this->context['market_id'] ?? null;
        $priceNode = null;

        // Сохраняем HTML для отладки
        //file_put_contents(storage_path("logs/html_market_{$component->id}.html"), $response->getBody());

        // === MARKETS ===
        switch ($marketId) {
            case 4: // KNS
                $priceNode = $response->filter('.price-info .price-val')->first();
                break;

            case 2: // REGARD
                $priceNode = $response->filter('span[class*="Price_price"]');
                break;

            case 1: // CITILINK
                $priceNode = $response->filter('[data-meta-price]')->first();
                break;

            default:
                logger()->warning("⛔ Неизвестный market_id: {$marketId} для компонента {$component->id}");
                yield ParseResult::item([]);
                return;
        }

        if (!$priceNode || $priceNode->count() === 0) {
            logger()->warning("⛔ [market_id: $marketId] Не найдена цена у компонента {$component->name} [ID {$component->id}]");

            ParsedData::create([
                'component_id' => $component->id,
                'source' => $this->getSourceName($marketId),
                'availability' => 0,
            ]);

            yield ParseResult::item([]);
            return;
        }

        $priceText = $priceNode->attr('data-meta-price') ?? $priceNode->text();
        $price = (int) preg_replace('/\D+/', '', $priceText);

        ParsedData::create([
            'component_id' => $component->id,
            'source' => $this->getSourceName($marketId),
            'price' => $price,
            'availability' => 1,
        ]);

        $component->update(['price' => $price]);

        logger()->info("✅ Цена обновлена: {$component->name} — {$price} ₽ [market_id {$marketId}]");

        yield ParseResult::item([]);
    }

    private function getSourceName(int $marketId): string
    {
        return match ($marketId) {
            1 => 'citilink',
            2 => 'regard',
            4 => 'kns',
            default => 'unknown',
        };
    }
}