<?php

namespace Pnl\App;

class SettingsProvider
{
    private array $config;

    public function load(string $file): void
    {
        if (!file_exists($file)) {
            throw new \RuntimeException('Settings file not found');
        }

        $content = file_get_contents($file);

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

        return $config;
    }
}
