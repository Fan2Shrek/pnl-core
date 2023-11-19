<?php

namespace Pnl\Installer;

use Pnl\Client\AbstractClient;

class GithubApi extends AbstractClient
{
    public function __construct(private readonly string $baseUrl)
    {
    }

    public function request(string $path, array $body = [], array $header = []): mixed
    {
        $path = $this->baseUrl . $path;

        return parent::request($path, $body, $header);
    }
}
