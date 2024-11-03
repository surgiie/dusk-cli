<?php

namespace App\Support;

use Illuminate\Console\Command;
use Laravel\Dusk\Browser;
use NunoMaduro\LaravelConsoleDusk\ConsoleBrowser;

class ConsoleDuskBrowser extends ConsoleBrowser
{
    public function __construct(Command $command, Browser $browser)
    {
        parent::__construct($command, $browser);

        // see https://github.com/laravel/dusk/issues/781
        $this->browser->resolver->prefix = 'html';
    }
}
