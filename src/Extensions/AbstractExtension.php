<?php

namespace Pnl\Extensions;

abstract class AbstractExtension implements ExtensionInterface
{
    private bool $isBooted = false;

    private string $name;

    private array $commands = [];

    public function boot(): void
    {
        $this->isBooted = true;
    }

    final public function isBooted(): bool
    {
        return $this->isBooted;
    }

    final public function getCommands(): array
    {
        return $this->commands;
    }

    final public function getName(): string
    {
        if ('' === $this->name) {
            throw new \Exception('Extension name not set');
        }

        return $this->name;
    }
}
