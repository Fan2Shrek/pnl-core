<?php

namespace Pnl\Extensions;

interface ExtensionInterface
{
    public function boot(): void;

    public function isBooted(): bool;

    public function getCommands(): array;

    public function getName(): string;
}
