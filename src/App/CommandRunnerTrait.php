<?php

namespace Pnl\App;

use Pnl\App\CommandInterface;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\ConsoleOutput;

trait CommandRunnerTrait
{
    private array $commandList = [];

    public function hasCommandName(string $commandName): bool
    {
        return isset($this->commandList[$commandName]);
    }

    public function addCommand(CommandInterface $command): void
    {
        if (!$this->hasCommand($command)) {
            $this->commandList[$command->getName()] = $command;
        }
    }

    public function hasCommand(CommandInterface $command): bool
    {
        return in_array($command->getName(), $this->commandList);
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
