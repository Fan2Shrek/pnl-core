<?php

namespace Pnl\Installer;

use Pnl\Console\Output\Style\CustomStyle;

class PreInstaller implements InstallerInterface
{
    private ?CustomStyle $style = null;

    public function __construct(private readonly GithubApi $client)
    {
    }

    public function setupStyle(CustomStyle $style): void
    {
        $this->style = $style;
    }

    public function install(string $gitLink): void
    {
        $httpLink = $this->getHttpLink($gitLink);

        $repositoryClient = RepositoryApi::createFromHttpLink($this->client, $httpLink);
        $result = $repositoryClient->getFileContent('pnl.json');
        $config = $this->extractConfig($result);

        if ($this->style !== null) {
            $this->style->writeWithStyle('Name found : ', 'green');
            $this->style->writeWithStyle($config->name, 'basic');
        }
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
