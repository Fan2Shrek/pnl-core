<?php

namespace Pnl\Installer\COR;

use Pnl\Installer\GithubApi;
use Pnl\Installer\PnlConfig;
use Pnl\Installer\RepositoryApi;

class ConfigUpdater extends AbsractInstaller
{
    public function __construct(private readonly GithubApi $client)
    {
    }

    public function install(PnlConfig $pnlConfig): PnlConfig
    {
        $file = __DIR__ . '/../../../config/settings.json';
        $httpLink = $this->getHttpLink($pnlConfig->gitlink);

        $repositoryClient = RepositoryApi::createFromHttpLink($this->client, $httpLink);

        try {
            $config = $repositoryClient->getFileContent('config/settings.json');
        } catch (\Exception $e) {
            return $pnlConfig;
        }

        if (!file_exists($file)) {
            touch($file);
        }

        $newSettings = $this->extractConfig($config);

        $settings = json_decode(file_get_contents($file), true);

        $settingsName = end(explode(' ', $pnlConfig->name));

        $settings[strtolower($settingsName)] = $newSettings;

        $content = json_encode($settings, JSON_PRETTY_PRINT);

        file_put_contents($file, $content);

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
    private function extractConfig(array $result): array
    {
        $burpConf = base64_decode($result['content']);
        $converted = json_decode($burpConf, true);

        return $converted;
    }
}
