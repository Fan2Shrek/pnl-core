<?php

namespace Pnl\Installer\COR;

use Pnl\Installer\PnlConfig;

class ClassInstaller extends AbsractInstaller
{
    public function install(PnlConfig $pnlConfig): PnlConfig
    {
        return $this->proccessInstall($pnlConfig);
    }

    private function proccessInstall(PnlConfig $pnlconfig): PnlConfig
    {
        $installer = new $pnlconfig->installer;
        if (!$installer instanceof InstallerInterface) {
            throw new \Exception('Installer must be instance of InstallerInterface');
        }

        $installer->setStyle($this->style);
        $installer->install($pnlconfig);

        return $pnlconfig;
    }
}
