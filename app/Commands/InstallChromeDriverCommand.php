<?php

namespace App\Commands;

use Laravel\Dusk\Console\ChromeDriverCommand;

class InstallChromeDriverCommand extends ChromeDriverCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'install:chrome-driver
                            {version?}
                            {--all : Install a ChromeDriver binary for every OS}
                            {--detect : Detect the installed Chrome / Chromium version}
                            {--proxy= : The proxy to download the binary through (example: "tcp://127.0.0.1:9000")}
                            {--ssl-no-verify : Bypass SSL certificate verification when installing through a proxy}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install the chrome dusk driver.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return parent::handle();
    }
}
