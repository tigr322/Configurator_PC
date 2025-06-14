<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RoachPHP\Roach;
use RoachPHP\Spider\Configuration\Overrides;
use App\Models\Component;

class UpdateComponentPrices extends Command
{
    protected $signature = 'prices:update';
    protected $description = 'Обновляет цены комплектующих с сайтов через RoachPHP';

    public function handle()
    {
        logger()->info("Команда запущена");

        $components = Component::whereNotNull('shop_url')->get();

        foreach ($components as $component) {
            logger()->info("Старт парсинга для компонента {$component->id}");

            Roach::startSpider(
                \App\Spiders\PriceUpdateSpider::class,
                new Overrides(
                    startUrls: [$component->shop_url]
                ),
                context: [
                    'component_id' => $component->id,
                    'market_id' => $component->market_id
                ]
            );
           
        }

        $this->info("Все цены обновлены.");
    }
}