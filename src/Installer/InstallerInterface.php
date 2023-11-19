<?php

namespace Pnl\Installer;

interface InstallerInterface
{
    public function install(string $gitLink): void;
}
