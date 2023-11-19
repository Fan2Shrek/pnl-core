<?php

namespace Pnl\Installer\COR;

use Pnl\Console\Output\Style\CustomStyle;

abstract class AbsractInstaller implements InstallerInterface, CORInterface
{
    private CORInterface $next;

    protected ?CustomStyle $style = null;

    abstract public function install(string $name): ?string;

    public function linkWith(CORInterface $next): CORInterface
    {
        $this->next = $next;

        return $next;
    }

    public function setupStyle(?CustomStyle $style): void
    {
        $this->style = $style;
    }

    public function check(string $info): bool
    {
        $this->setupStyle($this->style);
        $info = $this->install($info);

        if ($this->next === null) {
            return true;
        }

        return $this->next->check($info);
    }
}
