<?php

namespace Pnl\Runtime\Runner;

use Pnl\Application;

class AppRunner implements RunnerInterface
{
    public function __construct(private Application $app)
    {
    }

    public function run(?array $args = null): void
    {
        if (null === $args) {
            $args = $_SERVER['argv'];
            array_shift($args);
        }

        $this->app->run($args);
    }
}
