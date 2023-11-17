<?php

namespace Pnl\Runtime\Resolver;

class BaseResolver implements ResolverInterface
{
    public function __construct(private \Closure $closure, private array $args = [])
    {
    }

    public function resolve(): array
    {
        return [$this->closure, $this->args];
    }
}
