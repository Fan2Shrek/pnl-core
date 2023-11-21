<?php

namespace Pnl\App;

use Pnl\App\CommandInterface;
use Pnl\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

trait CommandRunnerTrait
{
    public function hasCommandName(string $commandName): bool
    {
        return isset($this->commandList[$commandName]);
    }

    public function getCommand(string $commandName): CommandInterface
    {
        return $this->commandList[$commandName];
    }

    public function executeCommand(CommandInterface $command, InputInterface $input): void
    {
        $args = $this->getInputResolver()->resolve($command, $input);

        $command($args, new ConsoleOutput());
    }
}
