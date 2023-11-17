<?php

namespace Pnl\App;

use Pnl\Console\Input\ArgumentBag;

abstract class AbstractCommand implements CommandInterface
{
    protected const NAME = '';

    public static function getArguments(): ArgumentBag
    {
        return new ArgumentBag();
    }

    public function getName(): string
    {
        if (empty(static::NAME)) {
            throw new \Exception(sprintf('Command %s does not have a name :(', static::class));
        }

        return static::NAME;
    }
}
