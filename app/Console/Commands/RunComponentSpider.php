<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RoachPHP\Roach;
use App\Spiders\ComponentSpider;

class RunComponentSpider extends Command
{
    protected $signature = 'spider:components';
    protected $description = 'Запуск парсера комплектующих';

    public function handle()
    {
        Roach::startSpider(ComponentSpider::class);
        $this->info('Парсинг завершён!');
    }
}
