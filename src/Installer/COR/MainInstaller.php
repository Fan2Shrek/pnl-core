<?php

namespace Pnl\Installer\COR;

use Pnl\Installer\PnlConfig;

class MainInstaller extends AbsractInstaller
{
    public function install(PnlConfig $pnlConfig): PnlConfig
    {
        return $this->proccessInstall($pnlConfig);
    }

    private function proccessInstall(PnlConfig $pnlConfig): PnlConfig
    {
        $this->style->writeWithStyle('Looking for composer...', 'green');

        $composer = exec('which composer');

        if (!$composer) {
            throw new \Exception('Composer not found');
        }

        $this->style->writeln('');
        $this->style->writeWithStyle(sprintf('✅ Composer find at %s', $composer), 'basic');
        $this->style->use('basic');
        $this->style->writeln(sprintf('⌛ Running composer require %s with version %s', $pnlConfig->composerName, $pnlConfig->version));
        $this->style->writeln('');
        $this->style->writeln('');

        exec(sprintf('composer require %s:%s', $pnlConfig->composerName, $pnlConfig->version));

        $this->style->writeln('');
        $this->style->writeWithStyle('Successfully install ', 'green');
        $this->style->writeWithStyle($pnlConfig->composerName, 'basic');
        $this->style->writeWithStyle(' with ', 'green');
        $this->style->writeWithStyle($pnlConfig->version, 'basic');
        $this->style->writeWithStyle(sprintf(' version 🎉', $pnlConfig->composerName, $pnlConfig->version), 'green');
        $this->style->use('basic');
        $this->style->writeln('');

        return $pnlConfig;
    }
}
