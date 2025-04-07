<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use App\Models\Component;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class ComponentSpider extends BasicSpider
{
    public array $startUrls = [
        'https://www.citilink.ru/catalog/processory/?ref=mainmenu_plate',
    ];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        [UserAgentMiddleware::class, [
            "userAgent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36"
        ]],
    ];
    private function parseCompatibilityData(string $title): array
{
    $data = [];

   
    if (preg_match('/Форм-фактор\s+([A-Z]+)\b/u', $title, $matches)) {
        $data['form_factor'] = $matches[1];
    }

  
    if (preg_match('/(Socket\s*AM[2345]|LGA\s*[\d]+|AM[2345])/ui', $title, $matches)) {
        
        $socket = preg_replace('/\s*Socket\s*/i', '', $matches[1]); 
        $socket = preg_replace('/\s+/', '', $socket); 
        $data['socket'] = $socket;
    }
    
    

    if (preg_match('/чипсет[:\s]+([A-Z0-9\s\-]+)/ui', $title, $matches)) {
        $data['chipset'] = trim($matches[1]);
    }

   
    if (stripos($title, 'CrossFire') !== false) {
        $data['multi_gpu'] = 'CrossFire';
    } elseif (stripos($title, 'SLI') !== false) {
        $data['multi_gpu'] = 'SLI';
    }

    if (preg_match('/Память\s+[0-9хx]+\s+(DDR[0-9])/ui', $title, $matches)) {
        $data['memory_type'] = $matches[1];
    }

    return $data;
}

    public function parse(Response $response): Generator
    {
        $items = [];

        // Ищем все товары
        $response->filter('div[data-meta-name="ProductHorizontalSnippet"]')->each(function ($node) use (&$items) {
            $name = $node->filter('a[title]')->count()
                ? trim($node->filter('a[title]')->attr('title'))
                : 'N/A';
                
            $name = preg_replace('/^(Материнская плата|Процессор|Видеокарта|Оперативная память|Жесткий диск|SSD|Кулер|Корпус|Блок питания)\s*/ui', '', $name);
            $brand = 'N/A';
            if (preg_match('/^([^\s]+)\s/', $name, $matches)) {
                $brand = $matches[1]; // первый кусок — бренд
            }
            $title = $node->filter('ul')->count()
                ? trim($node->filter('ul')->text())
                : 'N/A';
           
            $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
            
            $compatibilityData = $this->parseCompatibilityData($title);
            $price = $node->filter('span[data-meta-name="Snippet__price"] > span')->count()
                ? trim($node->filter('span[data-meta-name="Snippet__price"] > span')->attr('data-meta-price'))
                : 'N/A';
        
            // Поиск картинки
            $img = 'N/A';
            $shop_url = $node->filter('a[title]')->count()
    ? 'https://www.citilink.ru' . $node->filter('a[title]')->attr('href')
    : 'N/A';

            $imgNode = $node->filter('div[data-meta-name="Snippet__images"] img')->first();
            $img = $imgNode->count() ? $imgNode->attr('src') : 'N/A';
            // В Laravel (при сохранении компонента)
            $imageContents = file_get_contents($img);
            $foto = Str::uuid() . '.jpg';
            $filename = 'products/'.$foto ;
            Storage::disk('public')->put($filename, $imageContents);
          
        
            $items[] = [
                'category_id'=> 1,
                'name' => $name,
                'brand' => $brand,
               'compatibility_data' => json_encode($compatibilityData, JSON_UNESCAPED_UNICODE),
                'price' => $price,
                'shop_url'=> $shop_url,
                'image_url' =>$foto,
            ];
        });
        
        // Сохраняем в JSON
        $jsonPath = storage_path('app/parsed_data.json');
        file_put_contents($jsonPath, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Возвращаем каждый товар через yield
        foreach ($items as $item) {
            yield $this->item($item);
        }
        foreach ($items as $item) {
            Component::create($item); // Eloquent insert
            yield $this->item($item);
        }
    }
}
