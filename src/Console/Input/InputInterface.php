<?php

namespace Pnl\Console\Input;

interface InputInterface
{
    public function __get(string $name): mixed;

    /** @return array<string, mixed> */
    public function getAllArguments(): array;

    public function hasArgument(string $name): bool;

    public function haveNameless(): bool;

    public function getNameless(): mixed;
}
