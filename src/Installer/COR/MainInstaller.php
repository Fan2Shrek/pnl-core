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
        $this->writeWithStyle('Looking for composer...', 'green');

        $composer = exec('which composer');

        if (!$composer) {
            throw new \Exception('Composer not found');
        }

        $this->writeln('');
        $this->writeWithStyle(sprintf('✅ Composer find at %s', $composer), 'basic');
        $this->use('basic');
        $this->writeln(sprintf('⌛ Running composer require %s with version %s', $pnlConfig->composerName, $pnlConfig->version));
        $this->writeln('');
        $this->writeln('');

        exec(sprintf('composer require %s:%s', $pnlConfig->composerName, $pnlConfig->version));

        $this->writeln('');
        $this->writeWithStyle('Successfully install ', 'green');
        $this->writeWithStyle($pnlConfig->composerName, 'basic');
        $this->writeWithStyle(' with ', 'green');
        $this->writeWithStyle($pnlConfig->version, 'basic');
        $this->writeWithStyle(' version 🎉', 'green');
        $this->use('basic');
        $this->writeln('');

        return $pnlConfig;
    }
}
