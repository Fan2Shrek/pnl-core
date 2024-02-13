<?php

namespace Pnl\App\Command;

use Pnl\App\AbstractCommand;
use Pnl\Console\Input\ArgumentBag;
use Pnl\Console\Input\ArgumentType;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\OutputInterface;
use Pnl\Watcher\Watcher;

class WatchCommand extends AbstractCommand
{
    protected const NAME = 'watch';

    public function getDescription(): string
    {
        return 'Watch files';
    }


    public static function getArguments(): ArgumentBag
    {
        $bag = new ArgumentBag();

        $bag->add('command', true, 'Command to run', ArgumentType::STRING, nameless: true)
            ->add('path', false, 'Path to watch', ArgumentType::STRING, '.');

        return $bag;
    }

    public function __invoke(InputInterface $input, OutputInterface $output): void
    {
        $watcher = new Watcher();

        /** @var string */
        $path = $input->get('path');
        /** @var string */
        $command = $input->get('command');

        $watcher->watch($path, fn () => popen($command, 'r'));
    }
}
