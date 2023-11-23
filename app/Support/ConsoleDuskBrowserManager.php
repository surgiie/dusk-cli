<?php
namespace App\Support;

use App\Support\ConsoleDuskBrowserFactory;
use NunoMaduro\LaravelConsoleDusk\Manager;
use NunoMaduro\LaravelConsoleDusk\Drivers\Chrome;
use NunoMaduro\LaravelConsoleDusk\Contracts\Drivers\DriverContract;
use NunoMaduro\LaravelConsoleDusk\Contracts\ConsoleBrowserFactoryContract;

class ConsoleDuskBrowserManager extends Manager
{
    protected $driver;

    protected $browserFactory;

    public function __construct(DriverContract $driver = null, ConsoleBrowserFactoryContract $browserFactory = null)
    {
        $this->driver = $driver ?: new Chrome();
        $this->browserFactory = $browserFactory ?: new ConsoleDuskBrowserFactory();
    }
}
