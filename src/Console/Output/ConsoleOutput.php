<?php

namespace Pnl\Console\Output;

class ConsoleOutput implements OutputInterface
{
    public function writeln(string $message): void
    {
        $this->write($message, true);
    }

    public function write(string $message, bool $newline = false): void
    {
        echo $newline ? PHP_EOL : '';
        echo $message;
    }
}
