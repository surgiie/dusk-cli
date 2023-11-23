<?php

namespace App\Support;

use Laravel\Prompts\Spinner;
use Surgiie\Console\Command;

abstract class BaseCommand extends Command
{
    /**
     * Run a task using the given title and callback.
     * @param string  $title
     * @param \Closure|callable|null  $task
     * @return bool|null
     */
    public function task(string $title = '', $task = null): bool
    {
        $result = (new Spinner($title))->spin(
            $task,
            $title,
        );

        $this->output->writeln(
            "  $title: ".($result !== false ? '<info>Successful</info>' : '<fg=red>Failed</fg=red>')
        );

        return $result !== false;
    }
}
