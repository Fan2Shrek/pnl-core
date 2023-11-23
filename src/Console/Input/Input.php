<?php

namespace Pnl\Console\Input;

class Input implements InputInterface
{
    /** @var array<string, mixed>> */
    private array $argumentsList = [];

    private mixed $nameless = null;

    /**
     * @param array<string> $args
     */
    public function __construct(array $args = [])
    {
        $this->argumentsList = $this->parseArguments($args);
    }

    public function get(string $name): mixed
    {
        if ($this->hasArgument($name)) {
            return $this->argumentsList[$name];
        }

        return null;
    }

    public function getNameless(): mixed
    {
        return $this->nameless;
    }

    public function haveNameless(): bool
    {
        return null !== $this->nameless;
    }

    public function getAllArguments(): array
    {
        return $this->argumentsList;
    }

    public function hasArgument(string $name): bool
    {
        return isset($this->argumentsList[$name]);
    }

    /**
     * @param array<string> $args
     *
     * @return array<string, mixed>
     */
    private function parseArguments(array $args): array
    {
        $arguments = [];

        foreach ($args as $value) {
            if (str_starts_with($value, '--') && str_contains($value, '=')) {
                preg_match('/--(.*)=(.*)/', $value, $matches);

                $arguments[$matches[1]] = $matches[2];

                continue;
            } elseif (str_starts_with($value, '--')) {
                preg_match('/--(.*)/', $value, $matches);

                $arguments[$matches[1]] = true;
            } else {
                $this->nameless = $value;
            }
        }

        return $arguments;
    }
}
