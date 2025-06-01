<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RoachPHP\Roach;
use App\Spiders\ComponentRegardSpider;
use App\Spiders\ComponentKnsSpider;
class RunComponentSpider extends Command
{
    protected $signature = 'spider:components';
    protected $description = 'Запуск парсера комплектующих';

    public function handle()
    {
        Roach::startSpider(ComponentKnsSpider::class);
        $this->info('Парсинг завершён!');
    }
}
