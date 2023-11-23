<?php

namespace Pnl\Installer\COR;

use Pnl\Console\Output\OutputInterface;
use Pnl\Console\Output\Style\CustomStyle;
use Pnl\Installer\PnlConfig;

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

    public function write(string $message): void
    {
        if ($this->style === null) {
            return;
        }

        $this->style->write($message);
    }

    public function writeln(string $message): void
    {
        if ($this->style === null) {
            return;
        }

        $this->style->writeln($message);
    }

    public function writeWithStyle(string $message, string $style): void
    {
        if ($this->style === null) {
            return;
        }

        $this->style->writeWithStyle($message, $style);
    }

    public function use(string $style): void
    {
        if ($this->style === null) {
            return;
        }

        $this->style->use($style);
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
