<?php

namespace Pnl\Runtime;

use Pnl\Runtime\Runner\RunnerInterface;
use Pnl\Runtime\Resolver\ResolverInterface;

interface RuntimeInterface
{
    public function getResolver(callable $callable): ResolverInterface;

    public function getRunner(mixed $obj): RunnerInterface;
}
