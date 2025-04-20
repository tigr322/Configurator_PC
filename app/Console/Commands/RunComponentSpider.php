<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RoachPHP\Roach;
use App\Spiders\ComponentRegardSpider;

class RunComponentSpider extends Command
{
    protected $signature = 'spider:components';
    protected $description = 'Запуск парсера комплектующих';

    public function handle()
    {
        Roach::startSpider(ComponentRegardSpider::class);
        $this->info('Парсинг завершён!');
    }
}
