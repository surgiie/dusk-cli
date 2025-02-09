<?php

namespace App\Support;

use NunoMaduro\LaravelConsoleDusk\Contracts\ConsoleBrowserFactoryContract;
use NunoMaduro\LaravelConsoleDusk\Contracts\Drivers\DriverContract;
use NunoMaduro\LaravelConsoleDusk\Drivers\Chrome;
use NunoMaduro\LaravelConsoleDusk\Manager;

class ConsoleDuskBrowserManager extends Manager
{
    protected $driver;

    protected $browserFactory;

    public function __construct(?DriverContract $driver = null, ?ConsoleBrowserFactoryContract $browserFactory = null)
    {
        $this->driver = $driver ?: new Chrome;
        $this->browserFactory = $browserFactory ?: new ConsoleDuskBrowserFactory;
    }
}
