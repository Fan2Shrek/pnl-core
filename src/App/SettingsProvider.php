<?php

namespace Pnl\App;

class SettingsProvider
{
    /**
     * @var array<string, array<string, string>>
     */
    private array $config;

    public function load(string $file): void
    {
        if (!file_exists($file)) {
            throw new \RuntimeException('Settings file not found');
        }

        $content = file_get_contents($file);

        if (!$content) {
            throw new \RuntimeException('Settings file is empty');
        }

        /** @phpstan-ignore-next-line */
        $this->config = json_decode($content, true);
    }

    public function get(string $name): ?string
    {
        $keys = explode('.', $name);

        $config = $this->config;

        foreach ($keys as $key) {
            if (!isset($config[$key])) {
                return null;
            }

            $config = $config[$key];
        }

        /** @phpstan-ignore-next-line */
        return $config;
    }
}
