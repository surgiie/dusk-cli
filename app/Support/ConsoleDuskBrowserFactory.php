<?php

namespace App\Support;

use Illuminate\Console\Command;
use Laravel\Dusk\Browser;
use NunoMaduro\LaravelConsoleDusk\ConsoleBrowserFactory;
use NunoMaduro\LaravelConsoleDusk\Contracts\ConsoleBrowserContract;
use NunoMaduro\LaravelConsoleDusk\Contracts\Drivers\DriverContract;

class ConsoleDuskBrowserFactory extends ConsoleBrowserFactory
{
    public function make(Command $command, DriverContract $driver): ConsoleBrowserContract
    {
        $this->driver = $driver;

        return new ConsoleDuskBrowser($command, new Browser($this->createWebDriver()));
    }
}
