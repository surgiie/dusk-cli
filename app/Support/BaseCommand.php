<?php

namespace App\Support;

use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    /**
     * The options that are not defined on the command.
     */
    protected Collection $arbitraryOptions;

    /**
     * Constuct a new Command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->arbitraryOptions = collect();

        // Ignore validation errors for arbitrary options support.
        $this->ignoreValidationErrors();
    }

    /**
     * Initialize the command input/ouput objects.
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // parse arbitrary options for variable data.
        $tokens = $input instanceof ArrayInput ? invade($input)->parameters : invade($input)->tokens;
        $parser = new CommandOptionsParser($tokens);

        $definition = $this->getDefinition();

        foreach ($parser->parse() as $name => $data) {
            if (! $definition->hasOption($name)) {
                $this->arbitraryOptions->put($name, $data['value']);
                $this->addOption($name, mode: $data['mode']);
            }
        }
        //rebind input definition
        $input->bind($definition);
    }
}
