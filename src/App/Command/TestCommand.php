<?php

namespace Pnl\App\Command;

use Pnl\App\AbstractCommand;
use Pnl\Console\Input\ArgumentBag;
use Pnl\Console\Output\ANSI\TextColors;
use Pnl\Console\Input\ArgumentType;
use Pnl\Console\Output\Style\Style;
use Pnl\Console\Output\ANSI\Style as ANSIStyle;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\ANSI\BackgroundColor;
use Pnl\Console\Output\OutputInterface;

class TestCommand extends AbstractCommand
{
    protected const NAME = 'test';

    public static function getArguments(): ArgumentBag
    {
        return (new ArgumentBag())
            ->add('john', true, 'Test argument', ArgumentType::STRING)
            ->add('joe', true, 'Test argument 2', ArgumentType::BOOLEAN, true);
    }

    public function getDescription(): string
    {
        return 'Test command';
    }

    public function __invoke(InputInterface $input, OutputInterface $output): void
    {
        $style = new Style($output);

        $style->setColor(TextColors::BLACK)
            ->setBackground(BackgroundColor::WHITE)
            ->setStyle(ANSIStyle::ITALIC)
            ->start();

        /** @phpstan-ignore-next-line */
        $style->write(sprintf('%s de bz', $input->john));

        $style->end();

        $style->writeln(sprintf('jose'));
    }
}
