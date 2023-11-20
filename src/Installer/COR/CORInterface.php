<?php

namespace Pnl\Installer\COR;

interface CORInterface
{
    public function linkWith(CORInterface $next): CORInterface;

    public function check(mixed $payload): bool;
}
