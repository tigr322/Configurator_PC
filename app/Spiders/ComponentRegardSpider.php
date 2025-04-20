<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use App\Models\Component;
use Illuminate\Support\Facades\Log;

use RoachPHP\Http\Request;
use RoachPHP\Spider\ParseResult;
use RoachPHP\Spider\Parseable;
use RoachPHP\ItemPipeline\Item;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class ComponentRegardSpider extends BasicSpider{
    public array $startUrls = [];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        [UserAgentMiddleware::class, [
            "userAgent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36"
        ]],
    ];

 
    protected function parseCompatibilityData(string $text): array
{
    $data = [];
    if (preg_match_all('/\b(ATX|Micro[-\s]?ATX|mATX|Mini[-\s]?ITX|E[-\s]?ATX)\b/i', $text, $formFactorMatches)) {
        $formFactors = array_map(function ($f) {
            $normalized = str_replace(['-', ' '], '', $f);
            return $normalized === 'MICROATX' ? 'mATX' : ($normalized === 'MINIITX' ? 'Mini-ITX' : $normalized);
        }, $formFactorMatches[1]);
    
        $data['form_factor'] = array_values(array_unique($formFactors));
    }
    
    // Form factor
    

    // Socket
    if (preg_match('/LGA\s?(\d+)/i', $text, $lgaMatch)) {
        $data['socket'] = 'LGA' . $lgaMatch[1];
    } elseif (preg_match('/AM\s?(\d+)/i', $text, $amMatch)) {
        $data['socket'] = 'AM' . $amMatch[1];
    }
    if (preg_match('/(\d{3,5})\s?(МГц|MHz)/iu', $text, $matches)) {
        $data['speed'] = $matches[1] . 'MHz';
    }
    // Chipset
    if (preg_match('/(Intel|AMD)\s+(Z\d{3}|B\d{3}|H\d{3}|X\d{3}|A\d{3})/i', $text, $chipsetMatch)) {
        $data['chipset'] = $chipsetMatch[1] . ' ' . strtoupper($chipsetMatch[2]);
    }

    // Memory type (DDR)
    if (preg_match('/DDR(\d)/i', $text, $ddrMatch)) {
        $data['memory_type'] = 'DDR' . $ddrMatch[1];
    }

    // Интерфейсы (может быть несколько)
    $interfaces = [];
    if (preg_match_all('/M[\.\s-]?2/i', $text, $m2Match)) {
        $interfaces[] = 'M.2';
        $interfaces[] ='SATA';
    }
    if (preg_match_all('/SATA\s*(II|III|3\.0)?/i', $text, $sataMatch)) {
        $interfaces[] = 'SATA';
    }
    if (!empty($interfaces)) {
        $data['interface'] = array_unique($interfaces);
    }

    // PCI-E версия
    if (preg_match('/PCI[-\s]?E\s*(\d(?:\.\d)?)/i', $text, $pcieMatch)) {
        $data['pcie_version'] = $pcieMatch[1];
    }

    return $data;
}

    
    public function parse(Response $response): Generator
    {
        $items = [];
    
        $nodes = $response->filter('div[class*="Card_row"]');

        
        $nodes->each(function ($node) use (&$items) {
            // Название
            $nameNode = $node->filter('div[class*="CardText_title"]');
            $name = $nameNode->count() ? trim($nameNode->text()) : 'N/A';
    
            // Бренд
            $name = preg_replace('/^[\p{Z}\s\x{00A0}\x{FEFF}]*(Материнская плата|Процессор|Видеокарта|Оперативная память|Жесткий диск|SSD накопитель|Накопитель|Кулер|Корпус|Блок питания)[\p{Z}\s\x{00A0}\x{FEFF}]*/ui', '', $name);
            if (preg_match('/^([^\s]+)\s/', $name, $matches)) {
            $brand = $matches[1]; // первый кусок — бренд
            }
    
            // Характеристики
            $characteristicsNode = $node->filter('p[class*="CardText_text"]');
            $rawTitle = $characteristicsNode->count() ? trim($characteristicsNode->text()) : 'N/A';
            $compatibilityData= [];
            $compatibilityData = array_merge(
                $this->parseCompatibilityData($rawTitle),
                $this->parseCompatibilityData($name)
            );
            
            // Цена
            $priceNode = $node->filter('span[class*="CardPrice_price"]');
            $price = $priceNode->count()
                ? (int)preg_replace('/\D+/', '', $priceNode->text())
                : 0;
    
            // Ссылка на магазин
            $linkNode = $node->filter('a')->first();
            $shop_url = $linkNode->count()
                ? 'https://www.regard.ru' . $linkNode->attr('href')
                : 'N/A';
    
            // Картинка
            $imgNode = $node->filter('img[class*="CardImageSlider_image__W65ZP"]');
            $imgSrc = $imgNode->count()
                ? 'https://www.regard.ru' . $imgNode->attr('src')
                : null;
    
            $foto = null;
            if ($imgSrc && @file_get_contents($imgSrc)) {
                $imageContents = file_get_contents($imgSrc);
                $foto = Str::uuid() . '.jpg';
                $filename = 'products/' . $foto;
                Storage::disk('public')->put($filename, $imageContents);
            }
    
            $items[] = [
                'category_id'=>$this->context['category_id'],
                'name' => $name,
                'brand' => $brand,
                'market_id' =>$this->context['market_id'],
                'compatibility_data' => json_encode($compatibilityData, JSON_UNESCAPED_UNICODE),
                'price' => $price,
                'shop_url' => $shop_url,
                'image_url' => $foto,
                'characteristics' => $rawTitle,
            ];
        });
    
        // Сохраняем в JSON
        $jsonPath = storage_path('app/parsed_data_regard.json');
        file_put_contents($jsonPath, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
        // Возвращаем каждый товар через yield
        foreach ($items as $item) {
            Component::create($item);
            yield $this->item($item);
        }
    }
    
    
    
    


}