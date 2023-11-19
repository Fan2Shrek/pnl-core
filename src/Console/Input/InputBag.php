<?php

namespace Pnl\Console\Input;

class InputBag implements InputInterface
{
    /** @var array<string, mixed>> */
    private array $argumentsList = [];

    private mixed $nameless = null;

    public function __get(string $name): mixed
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

    public function addArgument(string $name, mixed $value, bool $isNameless = false): void
    {
        $this->argumentsList[$name] = $value;

        if ($isNameless) {
            if (null !== $this->nameless) {
                throw new \InvalidArgumentException('You can only have one nameless argument');
            }

            $this->nameless = $value;
        }
    }
}
