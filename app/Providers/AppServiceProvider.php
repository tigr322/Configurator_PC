<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Spiders\ComponentSpider;
use Illuminate\Console\Scheduling\Schedule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
        $this->app->bind(ComponentSpider::class, function () {
            return new ComponentSpider();
        });
      /*  $this->app->bind(ComponentSpider::class, function ($app) {
            return new ComponentSpider(
                $app['request']->get('categoryId'),
                $app['request']->get('sourceUrl')
            );
        });*/
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app()->booted(function () {
            app(Schedule::class)
                ->command('prices:update')
                ->everyMinute();
        });
    }
}
