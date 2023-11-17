<?php

namespace Pnl\App\Command;

use Pnl\App\AbstractCommand;
use Pnl\Console\Output\Style\Style;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\ANSI\TextColors;
use Pnl\Console\Output\OutputInterface;
use Pnl\Console\Output\ANSI\BackgroundColor;
use Pnl\Console\Output\ANSI\Style as ANSIStyle;
use Pnl\Console\Output\Style\CustomStyle;

class WelcomeCommand extends AbstractCommand
{
    protected const NAME = 'welcome';

    public function getDescription(): string
    {
        return 'Help command';
    }

    public function __invoke(InputInterface $input, OutputInterface $output): void
    {
        $style = new CustomStyle($output);

        $style->createStyle('subtitle')
            ->setColor(TextColors::GREEN)
            ->setBackground(BackgroundColor::BLACK)
            ->setStyle(ANSIStyle::ITALIC);

        $style->createStyle('basic')
            ->setColor(TextColors::BLACK)
            ->setBackground(BackgroundColor::WHITE);

        $style->createStyle('footer')
            ->setColor(TextColors::YELLOW)
            ->setBackground(BackgroundColor::RED);

        $style->writeWithStyle('Welcome to PNL Framework !', 'basic');
        $style->newLine();
        $style->writeWithStyle('Made By Fan2Shrek :)', 'subtitle');
        $style->writeln('Visit git@github.com:Fan2Shrek/PNL.git for more ');
        $style->use('footer');
        $style->writeln('Thank you for using PNL Framework !');
        $style->end();
    }
}
