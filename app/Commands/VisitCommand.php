<?php

namespace App\Commands;

use Laravel\Dusk\Browser;
use Illuminate\Support\Str;
use App\Support\BaseCommand;
use LaravelZero\Framework\Commands\Command;

class VisitCommand extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'visit {url : The url to visit.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Visit site and perform actions/make assertions.';

    /**
     * Parse function arguments from an option value.
     *
     * @param string $input
     * @return array
     */
    protected function parseActionArguments(string $value)
    {
        // Split on a comma not preceded by a backslash, optionally followed by spaces
        $parts = preg_split('/(?<!\\\\),\s*/', $value);

        return array_filter(array_map(function($part) {
            // Replace escaped comma with literal comma
            $value = str_replace('\\,', ',', trim($part));

            // cast string value to proper type if possible
            $value = match (true) {
                $value === 'null' => null,
                $value === 'true' => true,
                $value == 'false' => false,
                ctype_digit($value) => intval($value),
                is_numeric($value) => floatval($value),
                default => $value,
            };
            return $value;
        }, $parts));
    }

    /**
     * Configure the screenshot path.
     *
     * @param array $arguments
     * @return array
     */
    protected function configureScreenshot(array $arguments)
    {
        if(!isset($arguments[0])){
            throw new \InvalidArgumentException("Screenshot path not specified.");
        }

        Browser::$storeScreenshotsAt = dirname($arguments[0]);

        return $arguments;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if(! Str::isUrl($this->argument('url'))){
            $this->components->error("The url argument is invalid, must be full url including protocol.");
            return 1;
        }


        $this->browse(function ($browser){
            // see https://github.com/laravel/dusk/issues/781
            invade($browser)->browser->resolver->prefix = 'html';

            $browser = $browser->visit($this->argument("url"));

            foreach($this->arbitraryOptions as $name=>$option){
                $value = is_string($option) ? $option : "";

                $method = Str::camel($name);
                $arguments = $this->parseActionArguments($value);

                // allow screenshots to be saved to a custom path
                if($method == "screenshot"){
                    $arguments = $this->configureScreenshot($arguments);
                }

                try {
                    $browser->$method(...$arguments);
                }catch (\Throwable $e){
                    $this->exit($e->getMessage());
                }
            }
        });

        return 0;
    }
}
