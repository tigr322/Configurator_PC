<?php 
namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Component;

class ParserService
{
    public function parseFromUrl(string $url, int $categoryId)
    {
        $client = new Client();
        $response = $client->get($url);
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);

       
        $crawler->filter('.product')->each(function (Crawler $node) use ($categoryId) {
            $name = $node->filter('.product-title')->text();
            $brand = $node->filter('.brand')->text();
            $price = floatval(str_replace(['$', ' '], '', $node->filter('.price')->text()));
            $image = $node->filter('img')->attr('src');

            Component::create([
                'category_id' => $categoryId,
                'name' => $name,
                'brand' => $brand,
                'price' => $price,
                'image_url' => $image,
            ]);
        });

        return true;
    }
}
