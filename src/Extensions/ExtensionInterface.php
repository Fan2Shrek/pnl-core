<?php

namespace Pnl\Extensions;

use Pnl\App\CommandInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ExtensionInterface
{
    public function boot(ContainerBuilder $container): void;

    public function isBooted(): bool;

    /**
     * @return array<string, CommandInterface>
     */
    public function getCommands(): array;

    public static function getName(): string;
}
