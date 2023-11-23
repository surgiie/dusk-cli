<?php

namespace App\Commands;

use Illuminate\Support\Str;
use App\Support\BaseCommand;
use Laravel\Dusk\Browser;

class VisitCommand extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'visit {url : The url to visit.}';

    /**
     * Allow the command to accept arbitrary options.
     *
     * @var bool
     */
    protected bool $arbitraryOptions = true;

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Visit site and perform actions/make assertions.';

    /**
     * The validation rules for the input/options.
     */
    public function rules(): array
    {
        return [
            'url'=>[
                'required',
                'url',
            ],
        ];
    }
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
        $this->browse(function ($browser){
            // see https://github.com/laravel/dusk/issues/781
            invade($browser)->browser->resolver->prefix = 'html';

            $browser = $browser->visit($this->data->get("url"));

            global $argv;

            foreach($argv as $option){
                if(!str_starts_with($option, "--")){
                    continue;
                }

                $value = strpos($option, "=") != false ? Str::after($option, "=") : "";

                $option = Str::before(str_replace("--", "", $option), "=");

                $method = Str::camel($option);
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
