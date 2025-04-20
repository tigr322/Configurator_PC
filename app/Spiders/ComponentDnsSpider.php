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
use Symfony\Component\DomCrawler\Crawler;
class ComponentDnsSpider extends BasicSpider{
    public array $startUrls = ["https://www.onlinetrade.ru/catalogue/protsessory-c342/"];

  

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        [UserAgentMiddleware::class, [
            "userAgent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36"
        ]],
    ];

    private function parseCompatibilityData(string $text): array
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



// В начале метода parse
public function parse(Response $response): Generator
{
    Log::info('Начался парсинг страницы: ' . $response->getUri());

    $items = [];

    $nodes = $response->filter('div.indexGoods__item');

    $nodes->each(function ($node) use (&$items) {
        try {
            // Название
            $nameNode = $node->filter('a.indexGoods__item__name');
            $name = $nameNode->count() ? trim($nameNode->text()) : 'N/A';
    
            // Бренд
            $name = preg_replace('/^[\p{Z}\s\x{00A0}\x{FEFF}]*(Материнская плата|Процессор|Видеокарта|Оперативная память|Жесткий диск|SSD накопитель|Накопитель|Кулер|Корпус|Блок питания)[\p{Z}\s\x{00A0}\x{FEFF}]*/ui', '', $name);
            $brand = 'Неизвестно';
            if (preg_match('/^([^\s]+)\s/', $name, $matches)) {
                $brand = $matches[1];
            }
    
            // Характеристики (можно оставить как имя товара)
            $rawTitle = $name;
    
            $compatibilityData = array_merge(
                $this->parseCompatibilityData($rawTitle),
                $this->parseCompatibilityData($name)
            );
    
            // Цена
            $priceNode = $node->filter('div.indexGoods__item__price p');
            $price = $priceNode->count()
                ? (int)preg_replace('/\D+/', '', $priceNode->text())
                : 0;
    
            // Ссылка на товар
            $linkNode = $node->filter('a.indexGoods__item__name');
            $shop_url = $linkNode->count()
                ? 'https://www.dns-shop.ru' . $linkNode->attr('href')
                : 'N/A';
    
            // Картинка
            $imgNode = $node->filter('img.js__indexGoods__item__imageEntity');
            $imgSrc = $imgNode->count()
                ? $imgNode->attr('src')
                : null;
    
            $foto = null;
            if ($imgSrc && @file_get_contents($imgSrc)) {
                $imageContents = file_get_contents($imgSrc);
                $foto = Str::uuid() . '.jpg';
                $filename = 'products/' . $foto;
                //Storage::disk('public')->put($filename, $imageContents);
                Log::info("Изображение загружено: $filename");
            }
    
            $items[] = [
                'category_id' => 4,
                'name' => $name,
                'brand' => $brand,
                'compatibility_data' => json_encode($compatibilityData, JSON_UNESCAPED_UNICODE),
                'price' => $price,
                'shop_url' => $shop_url,
                'image_url' => $foto,
                'characteristics' => $rawTitle,
            ];
    
            Log::info("Добавлен товар: $name, цена: $price, бренд: $brand");
        } catch (\Throwable $e) {
            Log::error('Ошибка при обработке товара: ' . $e->getMessage());
        }
    });
    

    // Сохраняем в JSON
    $jsonPath = storage_path('app/parsed_data_dns.json');
    file_put_contents($jsonPath, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    Log::info("Все товары сохранены в: $jsonPath");

    foreach ($items as $item) {
        Log::info('Отправка товара через yield: ' . $item['name']);
        yield $this->item($item);
    }

    Log::info('Парсинг завершён успешно.');
}


}