<?php

namespace Pnl\Installer\COR;

use Pnl\Console\Output\Style\CustomStyle;
use Pnl\Installer\GithubApi;
use Pnl\Installer\PnlConfig;

class InstallerCOR implements InstallerInterface
{
    private ?CustomStyle $style = null;

    public function __construct(
        private readonly PreInstaller $preInstaller,
        private readonly GithubApi $githubApi,
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
            ->linkWith(new ClassInstaller())
            ->linkWith(new ExtensionsUpdater())
            ->linkWith(new ConfigUpdater($this->githubApi));
    }

    public function check(PnlConfig $pnlConfig): bool
    {
        if ($this->style !== null) {
            $this->setStyle($this->style);
        }
        $this->preInstaller->check($pnlConfig);

        return true;
    }
}
