<?php

namespace Pnl\App;

use Pnl\Console\Input\InputInterface;

interface CommandRunnerInterface
{
    public function hasCommandName(string $commandName): bool;

    public function getCommand(string $commandName): CommandInterface;

    public function executeCommand(CommandInterface $command, InputInterface $input): void;
}
