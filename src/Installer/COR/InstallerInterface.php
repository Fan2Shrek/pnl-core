<?php

namespace Pnl\Installer\COR;

interface InstallerInterface
{
    public function install(string $name): ?string;
}
