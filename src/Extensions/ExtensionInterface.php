<?php

namespace Pnl\Extensions;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ExtensionInterface
{
    public function boot(ContainerBuilder $container): void;

    public function isBooted(): bool;

    /**
     * @return string[]
     */
    public function getCommands(): array;

    public static function getName(): string;
}
