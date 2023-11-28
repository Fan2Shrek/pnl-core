<?php

namespace Pnl\Installer;

use Pnl\Client\AbstractClient;

class RepositoryApi extends AbstractClient
{
    public function __construct(
        private readonly GithubApi $githubApi,
        private readonly string $owner,
        private readonly string $name,
    ) {
    }

    public function request(string $path, array $body = [], array $header = []): mixed
    {
        return $this->githubApi->request($this->generateUrl() . $path, $body, $header);
    }

    public static function createFromHttpLink(GithubApi $githubApi, string $link): self
    {
        $splitted = explode('/', $link);

        return new self($githubApi, $splitted[3], $splitted[4]);
    }

    public function getFileContent(string $filePath): mixed
    {
        /** @phpstan-ignore-next-line */
        return $this->request(sprintf('%s%s', '/contents/', $filePath));
    }

    private function generateUrl(): string
    {
        return sprintf('/repos/%s/%s', $this->owner, $this->name);
    }
}
