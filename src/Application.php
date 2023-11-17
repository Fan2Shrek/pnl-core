<?php

namespace Pnl;

use Pnl\App\Exception\CommandNotFoundException;
use Pnl\Console\Input\Input;
use Pnl\Service\ClassAdapter;
use Pnl\Composer\ComposerContext;
use Composer\Autoload\ClassLoader;
use Pnl\App\CommandInterface;
use Pnl\App\DependencyInjection\AddCommandPass;
use Pnl\App\DependencyInjection\CommandCompiler;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\InputResolver;
use Pnl\Console\InputResolverInterface;
use Pnl\Console\Output\ConsoleOutput;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Application
{
    private ContainerBuilder $container;

    private ComposerContext $composerContext;

    /** @phpstan-ignore-next-line */
    private array $context = [];

    private bool $isBooted = false;

    /**
     * @var CommandInterface[]
     */
    private array $commandList = [];

    /** @phpstan-ignore-next-line */
    public function __construct(private ClassLoader $classLoader, array $context = [])
    {
        $this->context = $context;
    }

    /**
     * @param string[] $args
     * */
    public function run(array $args = []): void
    {
        $this->boot();

        if (empty($this->commandList)) {
            throw new \Exception('No commands found');
        }

        if (empty($args)) {
            $this->executeCommand($this->getCommand('help'), new Input($args));

            return;
        }

        if ($this->hasCommandName($args[0])) {
            $name = $args[0];
            array_shift($args);
            $this->executeCommand($this->getCommand($name), new Input($args));

            return;
        }

        throw new CommandNotFoundException(sprintf('Command %s not found', $args[0]));
    }

    private function boot(): void
    {
        if ($this->isBooted) {
            return;
        }

        if (!isset($this->composerContext)) {
            $this->loadComposerContext();
        }

        if (!isset($this->container)) {
            $this->initializeContainer();
        }

        if (empty($this->commandList)) {
            $this->registerCommands();
        }

        $this->isBooted = true;
    }

    private function initializeContainer(): void
    {
        $builder = new ContainerBuilder();

        $loader = new YamlFileLoader($builder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.yaml');

        $builder->addCompilerPass(new CommandCompiler());

        $builder->compile();

        $this->container = $builder;
    }

    private function registerCommands(): void
    {
        foreach ($this->container->findTaggedServiceIds('command') as $key => $command) {
            /** @phpstan-ignore-next-line */
            $this->addCommand($this->container->get($key));
        }
    }

    public function executeCommand(CommandInterface $command, InputInterface $input): void
    {
        $args = $this->getInputResolver()->resolve($command, $input);

        $command($args, new ConsoleOutput());
    }

    public function getInputResolver(): InputResolverInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->container->get(InputResolver::class);
    }

    private function loadComposerContext(): true
    {
        if (!file_exists('composer.json')) {
            throw new \Exception('composer.json not found');
        }

        $this->composerContext = ComposerContext::createFromJson('composer.json');

        return true;
    }

    private function hasCommandName(string $commandName): bool
    {
        return array_key_exists($commandName, $this->commandList);
    }

    private function getCommand(string $commandName): CommandInterface
    {
        return $this->commandList[$commandName];
    }


    public function addCommand(CommandInterface $command): void
    {
        if (!$this->hasCommand($command)) {
            $this->commandList[$command->getName()] = $command;
        }
    }

    public function hasCommand(CommandInterface $command): bool
    {
        return in_array($command->getName(), $this->commandList);
    }
}
