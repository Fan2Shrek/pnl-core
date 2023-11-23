<?php

namespace Pnl\Installer\COR;

use Pnl\Console\Output\Style\CustomStyle;
use Pnl\Installer\GithubApi;
use Pnl\Installer\PnlConfig;
use Pnl\Installer\RepositoryApi;

class PreInstaller extends AbsractInstaller
{
    public function __construct(private readonly GithubApi $client)
    {
    }

    public function install(PnlConfig $pnlConfig): PnlConfig
    {
        $httpLink = $this->getHttpLink($pnlConfig->gitlink);

        $repositoryClient = RepositoryApi::createFromHttpLink($this->client, $httpLink);
        /**
         * @var array{
         *  content: string,
         * }
         */
        $result = $repositoryClient->getFileContent('pnl.json');
        $config = $this->extractConfig($pnlConfig, $result);

        $this->writeWithStyle('Name found : ', 'green');
        $this->writeWithStyle($config->name, 'basic');

        $this->writeln('');
        $this->writeln('');

        return $pnlConfig;
    }

    private function getHttpLink(string $gitlink): string
    {
        $converted = str_replace('.git', '', $gitlink);
        $converted = str_replace(':', '/', $converted);
        $converted = str_replace('git@', 'https://', $converted);

        return $converted;
    }

    /**
     * @param array{
     *  content: string,
     * } $result
    */
    private function extractConfig(PnlConfig $pnlConfig, array $result): PnlConfig
    {
        $burpConf = base64_decode($result['content']);
        /**
         * @var array{
         *  name: string,
         *  main-class: string,
         *  composer-name: string,
         *  installer?: string,
         *  version: string,
         * }
         */
        $converted = json_decode($burpConf, true);

        return $pnlConfig->hydrateFromConf($converted);
    }
}
