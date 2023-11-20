<?php

namespace Pnl\Installer\COR;

use Pnl\Installer\PnlConfig;
use Pnl\Console\Output\Style\CustomStyle;

class InstallerCOR implements InstallerInterface
{
    private ?CustomStyle $style = null;

    public function __construct(
        private readonly PreInstaller $preInstaller,
    ) {
        $this->setupChain($preInstaller);
    }

    public function install(PnlConfig $pnlConfig): PnlConfig
    {
        return $this->preInstaller->install($pnlConfig);
    }

    public function setStyle(CustomStyle $style): void
    {
        $this->style = $style;
        $this->preInstaller->setupStyle($style);
    }

    private function setupChain(PreInstaller $preInstaller): void
    {
        $preInstaller
            ->linkWith(new MainInstaller())
            ->linkWith(new ClassInstaller());
    }

    public function check(PnlConfig $pnlConfig): bool
    {
        $this->setStyle($this->style);
        $this->preInstaller->check($pnlConfig);

        return true;
    }
}
