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
        if (!$installer instanceof AbsractInstaller) {
            throw new \Exception(sprintf('Installer must be instance of %s', AbsractInstaller::class));
        }

        if ("" === $pnlconfig->installer) {
            return $pnlconfig;
        }

        $this->writeln('');
        $this->writeWithStyle("✅ Installer class find at ", 'green');
        $this->writeWithStyle($pnlconfig->installer, 'basic');
        $this->writeln('');
        $this->writeWithStyle("🏃 Running installer ", 'green');
        $this->writeWithStyle($pnlconfig->installer, 'basic');
        $this->writeWithStyle("...", 'green');
        $this->writeln('');
        $this->writeln('');

        $installer->setupStyle($this->style);
        $installer->install($pnlconfig);

        $this->writeln('');

        $this->writeWithStyle("✅ Successfully installed ", 'green');
        $this->writeWithStyle($pnlconfig->name, 'basic');

        $this->writeln('');
        $this->writeln('');

        return $pnlconfig;
    }
}
