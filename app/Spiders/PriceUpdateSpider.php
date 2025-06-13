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
        $priceNode = $response->filter('[data-meta-price]')->first();

        if ($priceNode->count() === 0 || !$priceNode->attr('data-meta-price')) {
            $priceNode = $response->filter('[data-meta-name="PriceBlock__additional-price"] span')->first();
        }
        if ($priceNode->count() === 0 || !$priceNode->attr('data-meta-price')) {
          
            
            ParsedData::create([
                'component_id' => $component->id,
                'source' => 'citilink',
                'availability' => 0,
            ]);
        
            yield ParseResult::item([]);
            return;
        }
       

        $priceText = $priceNode->attr('data-meta-price');
        $price = (int) preg_replace('/\D+/', '', $priceText);
        
      
            ParsedData::create([
                'component_id' => $component->id,
                'source' => 'citilink',
                'price' => $price,
                'availability' => 1,
            ]);
            $component->update(['price' => $price]);

            
        
       

      

       

        yield ParseResult::item([]);
    }
}