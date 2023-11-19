<?php

namespace Pnl\Installer\COR;

use Pnl\Console\Output\Style\CustomStyle;

class InstallerCOR implements InstallerInterface
{
    private ?CustomStyle $style = null;

    public function __construct(
        private readonly PreInstaller $preInstaller,
        MainInstaller $mainInstaller
    ) {
        $this->setupChain($preInstaller, $mainInstaller);
    }

    public function install(string $name): ?string
    {
        return $this->preInstaller->install($name);
    }

    public function setStyle(CustomStyle $style): void
    {
        $this->style = $style;
        $this->preInstaller->setupStyle($style);
    }

    private function setupChain(PreInstaller $preInstaller, MainInstaller $mainInstaller): void
    {
        $preInstaller->linkWith($mainInstaller);
    }

    public function check(string $info): bool
    {
        $this->setStyle($this->style);
        $info = $this->preInstaller->check($info);

        return true;
    }
}
