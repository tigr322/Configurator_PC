<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ParserService
{
    public function parseFromUrl($url, $category_id)
    {
        $client = new Client();

    $headers = [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    ];

    try {
        // Отправляем запрос
        $response = $client->get($url, [
            'headers' => $headers
        ]);

        // Получаем HTML содержимое
        $html = (string) $response->getBody();

        // Загружаем HTML в Crawler
        $crawler = new Crawler($html);

        // Пример парсинга
        $crawler->filter('.product-card')->each(function (Crawler $node) use ($category_id) {
            // Логика парсинга
            $name = $node->filter('.product-title')->text();
            $price = $node->filter('.price')->text();
            
            // Сохранение в базу данных или другие действия
        });

        return 'Парсинг завершен';
    } catch (\Exception $e) {
        return 'Ошибка при выполнении запроса: ' . $e->getMessage();
    }
    }
}
