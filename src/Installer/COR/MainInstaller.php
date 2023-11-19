<?php

namespace Pnl\Installer\COR;

class MainInstaller extends AbsractInstaller
{
    public function install(string $name): ?string
    {
        dd($name);
        return $this->proccessInstall($name);
    }

    private function proccessInstall(string $name): ?string
    {
        /** @todo composer tout ca */
        return null;
    }
}
