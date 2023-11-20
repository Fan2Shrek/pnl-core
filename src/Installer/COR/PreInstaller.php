<?php

namespace Pnl\Installer\COR;

use Pnl\Installer\GithubApi;
use Pnl\Installer\PnlConfig;
use Pnl\Installer\RepositoryApi;
use Pnl\Console\Output\Style\CustomStyle;

class PreInstaller extends AbsractInstaller
{
    public function __construct(private readonly GithubApi $client)
    {
    }

    public function install(PnlConfig $pnlConfig): PnlConfig
    {
        $httpLink = $this->getHttpLink($pnlConfig->gitlink);

        $repositoryClient = RepositoryApi::createFromHttpLink($this->client, $httpLink);
        $result = $repositoryClient->getFileContent('pnl.json');
        $config = $this->extractConfig($pnlConfig, $result);

        if ($this->style !== null) {
            $this->style->writeWithStyle('Name found : ', 'green');
            $this->style->writeWithStyle($config->name, 'basic');
        }

        $this->style->writeln('');
        $this->style->writeln('');

        return $pnlConfig;
    }

    private function getHttpLink(string $gitlink): string
    {
        $converted = str_replace('.git', '', $gitlink);
        $converted = str_replace(':', '/', $converted);
        $converted = str_replace('git@', 'https://', $converted);

        return $converted;
    }

    private function extractConfig(PnlConfig $pnlConfig, array $result): PnlConfig
    {
        $burpConf = base64_decode($result['content']);

        return $pnlConfig->hydrateFromConf(json_decode($burpConf, true));
    }
}
