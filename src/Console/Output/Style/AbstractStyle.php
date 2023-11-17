<?php

namespace Pnl\Console\Output\Style;

use Pnl\Console\Output\ANSI\Style;
use Pnl\Console\Output\ANSI\TextColors;
use Pnl\Console\Output\OutputInterface;
use Pnl\Console\Output\ANSI\BackgroundColor;

abstract class AbstractStyle implements StyleInterface, OutputInterface
{
    protected TextColors $color = TextColors::DEFAULT;

    protected BackgroundColor $background = BackgroundColor::DEFAULT;

    protected Style $style = Style::RESET;

    protected readonly OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function writeln(string $message): void
    {
        $this->start();
        $this->output->writeln($message);
    }

    public function write(string $message, bool $newline = false): void
    {
        $this->start();
        $this->output->write($message, $newline);
    }

    public function setColor(TextColors $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function setBackground(BackgroundColor $background): static
    {
        $this->background = $background;

        return $this;
    }

    public function setStyle(Style $style): static
    {
        $this->style = $style;

        return $this;
    }

    public function start(): void
    {
        echo sprintf("\033[%s;%s;%sm", $this->style->value, $this->color->value, $this->background->value);
    }

    public function end(): void
    {
        echo sprintf("\033[%s;%s;%sm", STYLE::RESET->value, TextColors::RESET->value, BackgroundColor::RESET->value);
    }
}
