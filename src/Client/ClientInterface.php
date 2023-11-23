<?php

namespace Pnl\Client;

interface ClientInterface
{
    /**
     * @param array<mixed, mixed> $body
     * @param array<mixed, mixed> $header
    */
    public function request(string $path, array $body = [], array $header = []): mixed;
}
