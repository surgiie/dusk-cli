<?php

namespace App\Providers;

use App\Support\ConsoleDuskBrowserManager;
use Illuminate\Support\ServiceProvider;
use NunoMaduro\LaravelConsoleDusk\Contracts\ManagerContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {}

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ManagerContract::class, function ($app) {
            return new ConsoleDuskBrowserManager;
        });
    }
}
