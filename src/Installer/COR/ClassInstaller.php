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

        $this->style->writeln('');
        $this->style->writeWithStyle("✅ Installer class find at ", 'green');
        $this->style->writeWithStyle($pnlconfig->installer, 'basic');
        $this->style->writeln('');
        $this->style->writeWithStyle("🏃 Running installer ", 'green');
        $this->style->writeWithStyle($pnlconfig->installer, 'basic');
        $this->style->writeWithStyle("...", 'green');
        $this->style->writeln('');
        $this->style->writeln('');

        $installer->setupStyle($this->style);
        $installer->install($pnlconfig);

        $this->style->writeln('');

        $this->style->writeWithStyle("✅ Successfully installed ", 'green');
        $this->style->writeWithStyle($pnlconfig->name, 'basic');

        $this->style->writeln('');
        $this->style->writeln('');

        return $pnlconfig;
    }
}
