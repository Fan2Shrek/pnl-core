<?php

namespace Pnl\Installer\COR;

use Pnl\Installer\PnlConfig;
use Pnl\Console\Output\Style\CustomStyle;

abstract class AbsractInstaller implements InstallerInterface, CORInterface
{
    private ?AbsractInstaller $next = null;

    protected ?CustomStyle $style = null;

    abstract public function install(PnlConfig $pnlConfig): PnlConfig;

    public function linkWith(CORInterface $next): CORInterface
    {
        $this->next = $next;

        return $next;
    }

    public function setupStyle(?CustomStyle $style): void
    {
        $this->style = $style;
    }

    public function check(mixed $payload): bool
    {
        if (!$payload instanceof PnlConfig) {
            return false;
        }

        $info = $this->install($payload);

        if ($this->next === null) {
            return true;
        }

        $this->next->setupStyle($this->style);
        return $this->next->check($info);
    }
}
