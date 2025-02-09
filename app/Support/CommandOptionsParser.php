<?php

namespace App\Support;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class CommandOptionsParser
{
    /**
     * The options being parsed.
     */
    protected array $options = [];

    /**
     * Construct new CommandOptionsParser instance.
     */
    public function __construct(array $options)
    {
        $this->setOptions($options);
    }

    /**
     * Set the options to parse.
     */
    public function setOptions(array $options): static
    {
        $this->options = array_filter($options);

        return $this;
    }

    /**
     * Parse the set options.
     */
    public function parse(): array
    {
        $options = [];
        $iterable = $this->options;

        foreach ($iterable as $token) {

            preg_match('/--([^=]+)(=)?(.*)/', $token, $match);

            if (! $match) {
                continue;
            }

            $name = $match[1];
            $equals = $match[2] ?? false;
            $value = $match[3] ?? false;

            $optionExists = array_key_exists($name, $options);

            if ($optionExists && ($value || $equals)) {
                $options[$name] = [
                    'mode' => InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                    'value' => $options[$name]['value'] ?? [],
                ];
                $options[$name]['value'] = Arr::wrap($options[$name]['value']);
                $options[$name]['value'][] = $value;
            } elseif ($value) {
                $options[$name] = [
                    'mode' => InputOption::VALUE_REQUIRED,
                    'value' => $value,
                ];
            } elseif (! $optionExists) {
                $options[$name] = [
                    'mode' => ($value == '' && $equals) ? InputOption::VALUE_OPTIONAL : InputOption::VALUE_NONE,
                    'value' => ($value == '' && $equals) ? '' : true,
                ];
            } else {
                throw new InvalidArgumentException("The '$name' option has already been provided.");
            }
        }

        return $options;
    }
}
