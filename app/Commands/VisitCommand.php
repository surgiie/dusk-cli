<?php

namespace App\Commands;

use App\Support\BaseCommand;
use BadMethodCallException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use NunoMaduro\LaravelConsoleDusk\ConsoleBrowser;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\TextUI\Configuration\Registry;

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
     * @param  string  $input
     * @return array
     */
    protected function parseActionArguments(string $value)
    {
        // Split on a comma not preceded by a backslash, optionally followed by spaces
        $parts = preg_split('/(?<!\\\\),\s*/', $value);

        return array_filter(array_map(function ($part) {
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
     * @param  string  $path
     * @return array
     */
    protected function configureScreenshot(array $arguments)
    {
        if (! isset($arguments[0])) {
            throw new \InvalidArgumentException('Screenshot path not specified.');
        }

        Browser::$storeScreenshotsAt = dirname($arguments[0]);

        return $arguments;
    }

    /**
     * Call an assertion/action method.
     */
    protected function callBrowserMethod(ConsoleBrowser $browser, string $method, string|array $option)
    {
        $value = is_string($option) ? $option : '';

        $method = Str::camel($method);
        $arguments = $this->parseActionArguments($value);

        // allow screenshots to be saved to a custom path
        if ($method == 'screenshot') {
            $arguments = $this->configureScreenshot($arguments);
        }
        try {
            $browser->$method(...$arguments);
        } catch (BadMethodCallException) {
            $this->components->error("Invalid browser action method: $method");
            exit(1);
        } catch (ExpectationFailedException | \TypeError $e) {
            // certain assertion/browser methods throw a TypeError, for now if this occurs, handle it until proper ExpectationFailedException is thrown
            // see: https://github.com/nunomaduro/laravel-console-dusk/issues/41
            if ($e instanceof \TypeError && ! Str::contains($e->getMessage(), "PHPUnit\TextUI\Configuration\Configuration, null returned")) {
                throw $e;
            }

            exit(1);
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! Str::isUrl($this->argument('url'))) {
            $this->components->error('The url argument is invalid, must be full url including protocol.');

            return 1;
        }

        $this->browse(function ($browser) {
            // see https://github.com/laravel/dusk/issues/781
            invade($browser)->browser->resolver->prefix = 'html';

            $browser = $browser->visit($this->argument('url'));
            foreach ($this->arbitraryOptions as $method => $option) {
                $options = $option;
                if (! is_array($options)) {
                    $options = Arr::wrap($options);
                }

                foreach ($options as $option) {
                    $this->callBrowserMethod($browser, $method, $option);
                }

            }
        });

        return 0;
    }
}
