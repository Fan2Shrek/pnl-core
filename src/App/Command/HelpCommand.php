<?php

namespace Pnl\App\Command;

use Pnl\App\AbstractCommand;
use Pnl\App\CommandInterface;
use Pnl\App\SettingsProvider;
use Pnl\Console\Input\ArgumentBag;
use Pnl\Console\Input\ArgumentType;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\ANSI\Style as ANSIStyle;
use Pnl\Console\Output\ANSI\TextColors;
use Pnl\Console\Output\OutputInterface;
use Pnl\Console\Output\Style\CustomStyle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HelpCommand extends AbstractCommand
{
    protected const NAME = 'help';

    /**
     * @var array<string, CommandInterface[]>
     */
    private array $commandList = [];

    private ?CustomStyle $style = null;

    public function __construct(ContainerBuilder $container)
    {
        $this->getAllCommand($container);
    }

    public function getDescription(): string
    {
        return 'Show this help';
    }

    public static function getArguments(): ArgumentBag
    {
        return (new ArgumentBag())->add('command', false, 'The command name', ArgumentType::STRING, nameless: true);
    }

    private function getAllCommand(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('command') as $key => $tags) {
            if (!isset($tags[0]['extensions'])) {
                $extensions = 'unknown';
            } else {
                $extensions = $tags[0]['extensions'];
            }
            if ($key !== self::class) {
                /** @var AbstractCommand */
                $command = $container->get($key);
                $this->commandList[(string)$extensions][$command->getName()] = $command;
            }
        }

        $this->commandList['app'][self::NAME] = $this;
    }

    private function setStyle(OutputInterface $output): void
    {
        if ($this->style !== null) {
            return;
        }

        $style = new CustomStyle($output);

        $style->createStyle('name')
            ->setColor(TextColors::WHITE)
            ->setStyle(ANSIStyle::ITALIC);

        $style->createStyle('description')
            ->setColor(TextColors::GREEN)
            ->setStyle(ANSIStyle::BOLD);

        $this->style = $style;
    }


    public function __invoke(InputInterface $input, OutputInterface $output): void
    {
        $this->setStyle($output);

        if ($input->haveNameless()) {
            /** @phpstan-ignore-next-line */
            $this->getDetail($input->getNameless());

            return;
        }

        $this->showAllCommand();
    }

    private function getDetail(string $commandName): void
    {
        foreach ($this->commandList as $commands) {
            if (isset($commands[$commandName])) {
                $command = $commands[$commandName];
                $this->printCommand($command);

                return;
            }
        }

        return;
    }

    private function showAllCommand(): void
    {
        if (null === $this->style) {
            throw new \Exception(sprintf('Style is not set, you should call %s() before', 'setStyle'));
        }

        $this->style->write('Available commands :');
        $this->style->newLine();

        foreach ($this->commandList as $extension => $commands) {
            /** @phpstan-ignore-next-line */
            $this->style->newLine();
            /** @phpstan-ignore-next-line */
            $this->style->writeWithStyle(
                sprintf('%s extension :', ucfirst($extension)),
                'name'
            );
            foreach ($commands as $command) {
                /** @phpstan-ignore-next-line */
                $this->style->newLine();
                $this->printCommand($command, true);
            }
        }
    }

    private function printCommand(CommandInterface $command, bool $indent = false): void
    {
        if (null === $this->style) {
            throw new \Exception(sprintf('Style is not set, you should call %s() before', 'setStyle'));
        }

        $width = (int)exec('tput cols');

        $this->style->writeWithStyle(
            sprintf('%s%s :', $indent ? "\t" : "", ucfirst($command->getName())),
            'name'
        );

        $spaces = $width - strlen($command->getName()) - strlen($command->getDescription()) - 3 - 8;

        if ($spaces < 0) {
            $spaces = 0;
            $this->style->newLine();
        }

        $this->style->writeWithStyle(
            sprintf(
                '%s%s',
                str_repeat(' ', $spaces),
                $command->getDescription()
            ),
            'description'
        );
        $this->style->newLine();
    }
}
