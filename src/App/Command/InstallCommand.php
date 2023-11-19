<?php

namespace Pnl\App\Command;

use Pnl\App\AbstractCommand;
use Pnl\Installer\COR\PreInstaller;
use Pnl\Console\Input\ArgumentBag;
use Pnl\Console\Input\ArgumentType;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\ANSI\TextColors;
use Pnl\Console\Output\OutputInterface;
use Pnl\Console\Output\Style\CustomStyle;
use Pnl\Console\Output\ANSI\Style as ANSIStyle;
use Pnl\Installer\COR\InstallerCOR;

final class InstallCommand extends AbstractCommand
{
    protected const NAME = 'install';

    private ?CustomStyle $style = null;

    public function __construct(private InstallerCOR $installer)
    {
    }

    public function getDescription(): string
    {
        return 'Show this help';
    }

    public static function getArguments(): ArgumentBag
    {
        return (new ArgumentBag())
            ->add('git_link', true, 'The git link to add', ArgumentType::STRING, nameless: true)
            ->add('style', false, 'Print informations', ArgumentType::BOOLEAN, true);
    }

    private function setStyle(OutputInterface $output): void
    {
        if ($this->style !== null) {
            return;
        }

        $style = new CustomStyle($output);

        $style->createStyle('basic')
            ->setColor(TextColors::WHITE);

        $style->createStyle('green')
            ->setColor(TextColors::GREEN)
            ->setStyle(ANSIStyle::BOLD);

        $this->style = $style;
    }


    public function __invoke(InputInterface $input, OutputInterface $output): void
    {
        $this->setStyle($output);

        if ($input->style) {
            $this->installer->setStyle($this->style);
            $this->style->write(sprintf('Installing : '));
            $this->style->writeWithStyle($input->getNameless(), 'basic');
            $this->style->writeln("");
            $this->style->writeln("");
        }

        $this->installer->check($input->getNameless());
    }
}
