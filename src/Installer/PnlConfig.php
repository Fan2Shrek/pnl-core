<?php

namespace Pnl\Installer;

class PnlConfig
{
    public string $gitlink;

    public string $name;

    public string $composerName;

    public string $mainClass;

    public string $installer;

    public string $version;

    public function __construct(string $gitlink)
    {
        $this->gitlink = $gitlink;
    }

    /**
     * @param array{
     *  name: string,
     *  main-class: string,
     *  composer-name: string,
     *  installer?: string,
     *  version: string,
     * } $conf
     */
    public function hydrateFromConf(array $conf): static
    {
        $this->name = $conf['name'];
        $this->mainClass = $conf['main-class'];
        $this->composerName = $conf['composer-name'];
        $this->installer = $conf['installer'] ?? "";
        $this->version = $conf['version'];

        return $this;
    }
}
