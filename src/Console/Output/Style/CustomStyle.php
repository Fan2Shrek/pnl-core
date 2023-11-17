<?php

namespace Pnl\Console\Output\Style;

use Pnl\Console\Output\Style\Style;

class CustomStyle extends AbstractStyle
{
    /**
     * @var Style[]
     */
    private array $styles = [];

    private ?Style $currentStyle = null;

    public function has(string $name): bool
    {
        return isset($this->styles[$name]);
    }

    public function get(string $name): ?Style
    {
        if (!$this->has($name)) {
            return null;
        }

        return $this->styles[$name];
    }

    public function addStyle(string $name, Style $style): static
    {
        if ($this->has($name)) {
            throw new \Exception(sprintf('Style %s already exists', $name));
        }

        $this->styles[$name] = $style;

        return $this;
    }

    public function newLine(): void
    {
        $this->output->writeln('');
    }

    public function writeWithStyle(string $message, string $style = null): void
    {
        if (null === $style && null === $this->currentStyle) {
            $this->output->write($message);

            return;
        }

        if (null !== $style) {
            $this->currentStyle = $this->get($style);
        }

        /** @phpstan-ignore-next-line */
        $this->currentStyle->write($message);
    }

    public function use(?string $name = null): static
    {
        $this->currentStyle = null !== $name ? $this->get($name) : null;

        return $this;
    }

    public function createStyle(string $name): Style
    {
        $style = new Style($this->output);

        $this->currentStyle = $style;
        $this->addStyle($name, $style);

        return $style;
    }

    public function writeln(string $message): void
    {
        if (null !== $this->currentStyle) {
            $this->currentStyle->start();
            $this->currentStyle->writeln($message);

            return;
        }

        $this->output->writeln($message);
    }

    public function write(string $message, bool $newline = false): void
    {
        if (null !== $this->currentStyle) {
            $this->currentStyle->write($message, $newline);

            return;
        }

        $this->output->write($message, $newline);
    }
}
