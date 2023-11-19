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

    public function install(string $name): ?string
    {
        $httpLink = $this->getHttpLink($name);

        $repositoryClient = RepositoryApi::createFromHttpLink($this->client, $httpLink);
        $result = $repositoryClient->getFileContent('pnl.json');
        $config = $this->extractConfig($result);

        if ($this->style !== null) {
            $this->style->writeWithStyle('Name found : ', 'green');
            $this->style->writeWithStyle($config->name, 'basic');
        }

        $this->style->writeln('');
        $this->style->writeln('');

        return $config->name;
    }

    private function getHttpLink(string $gitlink): string
    {
        $converted = str_replace('.git', '', $gitlink);
        $converted = str_replace(':', '/', $converted);
        $converted = str_replace('git@', 'https://', $converted);

        return $converted;
    }

    private function extractConfig(array $result): PnlConfig
    {
        $burpConf = base64_decode($result['content']);

        return PnlConfig::createFromArray(json_decode($burpConf, true));
    }
}
