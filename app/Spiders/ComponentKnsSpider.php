<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use App\Models\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComponentKnsSpider extends BasicSpider
{
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
                $normalized = strtoupper(str_replace(['-', ' '], '', $f));
                return match ($normalized) {
                    'MICROATX', 'MATX' => 'mATX',
                    'MINIITX' => 'Mini-ITX',
                    default => $normalized,
                };
            }, $formFactorMatches[1]);

            $data['form_factor'] = array_values(array_unique($formFactors));
        }

        if (preg_match('/ATX12V/i', $text)) {
            $data['form_factor'][] = 'ATX';
            $data['form_factor'] = array_unique($data['form_factor']);
        }

        if (preg_match('/LGA\s?(\d+)/i', $text, $lgaMatch)) {
            $data['socket'] = 'LGA' . $lgaMatch[1];
        } elseif (preg_match('/AM\s?(\d+)/i', $text, $amMatch)) {
            $data['socket'] = 'AM' . $amMatch[1];
        }

        if (preg_match('/(\d{3,5})\s?(МГц|MHz)/iu', $text, $matches)) {
            $data['speed'] = $matches[1] . 'MHz';
        }

        if (preg_match('/(Intel|AMD)\s+(Z\d{3}|B\d{3}|H\d{3}|X\d{3}|A\d{3})/i', $text, $chipsetMatch)) {
            $data['chipset'] = $chipsetMatch[1] . ' ' . strtoupper($chipsetMatch[2]);
        }

        if (preg_match('/DDR(\d)/i', $text, $ddrMatch)) {
            $data['memory_type'] = 'DDR' . $ddrMatch[1];
        }

        $interfaces = [];
        if (preg_match_all('/M[\.\s-]?2/i', $text)) {
            $interfaces[] = 'M.2';
            $interfaces[] = 'SATA';
        }
        if (preg_match_all('/SATA\s*(II|III|3\.0)?/i', $text)) {
            $interfaces[] = 'SATA';
        }
        if (!empty($interfaces)) {
            $data['interface'] = array_unique($interfaces);
        }

        if (preg_match('/PCI[-\s]?E\s*(\d(?:\.\d)?)/i', $text, $pcieMatch)) {
            $data['pcie_version'] = $pcieMatch[1];
        }

        return $data;
    }

    public function parse(Response $response): Generator
    {
        $items = [];

        $nodes = $response->filter('div[itemtype="http://schema.org/Product"]');

        $nodes->each(function ($node) use (&$items) {
            $nameNode = $node->filter('[itemprop="name"]');
            $name = $nameNode->count() ? trim($nameNode->text()) : 'N/A';

            $descriptionNode = $node->filter('[itemprop="description"]');
            $description = $descriptionNode->count() ? trim($descriptionNode->text()) : '-';

            $priceNode = $node->filter('meta[itemprop="price"]');
            $price = $priceNode->count() ? (int)$priceNode->attr('content') : 0;

            $availabilityNode = $node->filter('link[itemprop="availability"]');
            $availability = $availabilityNode->count() ? trim($availabilityNode->attr('href')) : 'N/A';

            $skuNode = $node->filter('[itemprop="sku"]');
            $sku = $skuNode->count() ? trim($skuNode->text()) : 'N/A';

            $linkNode = $node->filter('a.img');
            $shop_url = $linkNode->count() ? 'https://www.kns.ru' . $linkNode->attr('href') : 'N/A';

            $imgNode = $node->filter('img[itemprop="image"]');
            $imgSrc = $imgNode->count() ? $imgNode->attr('src') : null;

            $foto = null;
            if ($imgSrc && @file_get_contents($imgSrc)) {
                $imageContents = file_get_contents($imgSrc);
                $foto = Str::uuid() . '.jpg';
                $filename = 'products/' . $foto;
                Storage::disk('public')->put($filename, $imageContents);
            }

            $brand = 'Unknown';
            if (preg_match('/^([\w\-]+)/u', $name, $matches)) {
                $brand = $matches[1];
            }

            $compatibilityData = array_merge(
                $this->parseCompatibilityData($description),
                $this->parseCompatibilityData($name)
            );

            $items[] = [
                'category_id' => $this->context['category_id'] ?? null,
                'name' => $name,
                'brand' => $brand,
                'market_id' => $this->context['market_id'] ?? null,
                'compatibility_data' => json_encode($compatibilityData, JSON_UNESCAPED_UNICODE),
                'price' => $price,
                'shop_url' => $shop_url,
                'image_url' => $foto,
                'characteristics' => $description,
                
            ];
        });

        $jsonPath = storage_path('app/parsed_data_kns.json');
        file_put_contents($jsonPath, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        foreach ($items as $item) {
            Component::create($item);
            yield $this->item($item);
        }
    }
}
