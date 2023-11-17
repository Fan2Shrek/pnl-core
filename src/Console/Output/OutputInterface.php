<?php

namespace Pnl\Console\Output;

use Pnl\Console\Output\TextColors;

interface OutputInterface
{
    public function writeln(string $message): void;

    public function write(string $message, bool $newline = false): void;


}
