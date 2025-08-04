<?php

namespace App\Spiders;

use Generator;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Component;
use Illuminate\Support\Facades\Log;

class ComponentDnsSpider extends BasicSpider
{
    public array $startUrls = [
        'https://www.dns-shop.ru/catalog/17a8a01d16404e77/processory/',
    ];

    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
        [UserAgentMiddleware::class, [
            "userAgent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36"
        ]],
    ];

    public function parse(Response $response): Generator
    {
        $url = 'https://www.dns-shop.ru/catalog/17a8a01d16404e77/processory/';
    
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36",
                    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                    "Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7",
                    "Connection: keep-alive"
                ]
            ]
        ]);
    
        $html = file_get_contents($url, false, $context);
    
        $path = storage_path('app/dns_raw.html');
        file_put_contents($path, $html);
    
        Log::info("DNS Spider: HTML сохранён по пути — $path");
    
        return yield from [];
    }
}