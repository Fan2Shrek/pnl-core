<?php

namespace Pnl\Composer;

readonly class ComposerContext
{
    public string $name;

    public string $type;

    /** @var array<string, string> */
    public array $autoload;

    /** @var string[] */
    public array $authors;

    /** @var string[] */
    public array $require;

    /** @var string[] */
    public array $require_dev;

    /**
     * @param array<string, string> $autoload
     * @param string[] $authors
     * @param string[] $require
     * @param string[] $require_dev
     */
    public function __construct(
        string $name = '',
        string $type = '',
        array $autoload = [],
        array $authors = [],
        array $require = [],
        array $require_dev = []
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->autoload = $autoload;
        $this->authors = $authors;
        $this->require = $require;
        $this->require_dev = $require_dev;
    }

    public static function createFromJson(string $path): self
    {
        $content = file_get_contents($path);

        if (!$content) {
            throw new \Exception(sprintf('The file %s does not exist', $path));
        }

        /** @var array{
         *  name: string,
         *  type: string,
         *  autoload: array<string, string>,
         *  authors?: string[],
         *  require: string[],
         *  require-dev?: string[]
         * }
         */
        $json = json_decode($content, true);

        $self = new self(
            $json['name'],
            $json['type'],
            $json['autoload'],
            $json['authors'] ?? [],
            $json['require'],
            $json['require-dev'] ?? []
        );

        return $self;
    }
}
