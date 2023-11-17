<?php

namespace Pnl\App;

use Pnl\Console\Input\ArgumentBag;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\OutputInterface;

interface CommandInterface
{
    public static function getArguments(): ArgumentBag;

    public function __invoke(InputInterface $input, OutputInterface $output): void;

    public function getName(): string;

    public function getDescription(): string;
}
