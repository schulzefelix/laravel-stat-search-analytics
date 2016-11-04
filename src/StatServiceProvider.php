<?php

namespace SchulzeFelix\Stat;

use Illuminate\Support\ServiceProvider;

class StatServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/laravel-stat-search-analytics.php' => config_path('laravel-stat-search-analytics.php'),
        ], 'config');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/laravel-sistrix.php', 'laravel-sistrix');

        $sistrixConfig = config('laravel-sistrix');



    }
}