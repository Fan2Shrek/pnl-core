<?php

namespace Pnl\Client;

interface ClientInterface
{
    public function request(string $path, array $body = [], array $header = []): mixed;
}
