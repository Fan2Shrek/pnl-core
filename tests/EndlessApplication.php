<?php

namespace Pnl\Test;

use Pnl\Application;
use Pnl\Watcher\Watcher;

class EndlessApplication extends Application
{
    /**
     * @param string[] $args
     */
    public function run(array $args = []): int
    {
        echo "\033[2J\033[;H";

        $watcher = new Watcher();

        $watcher->watch(
            __DIR__ . '/../src',
            function () use ($args) {
                $app = popen('./pnl ' . join(' ', $args), 'r');

                return $app;
            }
        );

        return 1;
    }
}
