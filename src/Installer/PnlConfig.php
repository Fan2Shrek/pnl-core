<?php

namespace Pnl\Installer;

readonly class PnlConfig
{
    public string $name;

    public string $mainClass;

    public string $installer;

    public static function createFromArray(array $conf): static
    {
        $pnlConfig = new static();

        $pnlConfig->name = $conf['name'];
        $pnlConfig->mainClass = $conf['main-class'];
        $pnlConfig->installer = $conf['installer'];

        return $pnlConfig;
    }
}
