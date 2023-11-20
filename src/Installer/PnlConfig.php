<?php

namespace Pnl\Installer;

readonly class PnlConfig
{
    public string $name;

    public string $composerName;

    public string $mainClass;

    public string $installer;

    public string $version;

    public function __construct(public string $gitlink)
    {
    }

    public function hydrateFromConf(array $conf): static
    {
        $this->name = $conf['name'];
        $this->mainClass = $conf['main-class'];
        $this->composerName = $conf['composer-name'];
        $this->installer = $conf['installer'];
        $this->version = $conf['version'];

        return $this;
    }
}
