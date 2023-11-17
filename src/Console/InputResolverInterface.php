<?php

namespace Pnl\Console;

use Pnl\App\CommandInterface;
use Pnl\Console\Input\InputInterface;

interface InputResolverInterface
{
    public function resolve(CommandInterface $command, InputInterface $arguments): InputInterface;
}
