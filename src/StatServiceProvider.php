<?php

namespace SchulzeFelix\Stat;

use Illuminate\Support\ServiceProvider;
use SchulzeFelix\Stat\Exceptions\InvalidConfiguration;

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
        $this->mergeConfigFrom(__DIR__.'/config/laravel-stat-search-analytics.php', 'laravel-stat-search-analytics');

        $statConfig = config('laravel-stat-search-analytics');

        $this->app->bind(StatClient::class, function () use ($statConfig) {
            if (empty($statConfig['key'])) {
                throw InvalidConfiguration::keyNotSpecified();
            }

            return StatClientFactory::createForConfig($statConfig);
        });

        $this->app->bind(Stat::class, function () {
            $client = app(StatClient::class);

            return new Stat($client);
        });

        $this->app->alias(Stat::class, 'laravel-stat-search-analytics');
    }
}
