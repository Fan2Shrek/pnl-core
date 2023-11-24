<?php

namespace Pnl\Test;

use Pnl\Application;
use Pnl\Test\Watcher\Watcher;

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
            fn () => parent::run($args)
        );

        return 1;
    }
}
