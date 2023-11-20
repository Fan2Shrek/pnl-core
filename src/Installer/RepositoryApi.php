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

    public static function createFromHttpLink(GithubApi $githubApi, string $link): static
    {
        $splitted = explode('/', $link);

        return new static($githubApi, $splitted[3], $splitted[4]);
    }

    public function getFileContent(string $filePath): mixed
    {
        return json_decode('{
            "name": "pnl.json",
            "path": "pnl.json",
            "sha": "c0feab86422f9cef84f9bf080ca16abf1f20535a",
            "size": 173,
            "url": "https://api.github.com/repos/Fan2Shrek/pnl-php/contents/pnl.json?ref=main",
            "html_url": "https://github.com/Fan2Shrek/pnl-php/blob/main/pnl.json",
            "git_url": "https://api.github.com/repos/Fan2Shrek/pnl-php/git/blobs/c0feab86422f9cef84f9bf080ca16abf1f20535a",
            "download_url": "https://raw.githubusercontent.com/Fan2Shrek/pnl-php/main/pnl.json",
            "type": "file",
            "content": "ewogICAgIm5hbWUiOiAiUG5sIHBocCIsCiAgICAiY29tcG9zZXItbmFtZSI6\nICJwbmwvcG5sLXBocCIsCiAgICAibWFpbi1jbGFzcyI6ICJQbmxcXFBubFBo\ncFxcUE5MIiwKICAgICJpbnN0YWxsZXIiOiAiUG5sXFxQbmxQaHBcXFBubElu\nc3RhbGxlciIsCiAgICAidmVyc2lvbiI6ICJkZXYtbWFpbiIKfQo=\n",
            "encoding": "base64",
            "_links": {
              "self": "https://api.github.com/repos/Fan2Shrek/pnl-php/contents/pnl.json?ref=main",
              "git": "https://api.github.com/repos/Fan2Shrek/pnl-php/git/blobs/c0feab86422f9cef84f9bf080ca16abf1f20535a",
              "html": "https://github.com/Fan2Shrek/pnl-php/blob/main/pnl.json"
            }
          }', true);

        return $this->request(sprintf('%s%s', '/contents/', $filePath));
    }

    private function generateUrl(): string
    {
        return sprintf('/repos/%s/%s', $this->owner, $this->name);
    }
}
