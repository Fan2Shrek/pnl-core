<?php

namespace Pnl\Installer\COR;

use Pnl\Installer\PnlConfig;

interface InstallerInterface
{
    public function install(PnlConfig $pnlConfig): PnlConfig;
}
