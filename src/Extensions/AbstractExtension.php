<?php

namespace Pnl\Extensions;

use Pnl\App\CommandInterface;
use Pnl\App\CommandRunnerInterface;
use Pnl\App\CommandRunnerTrait;
use Pnl\Console\InputResolver;
use Pnl\Console\InputResolverInterface;
use Pnl\Console\Input\Input;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class AbstractExtension implements ExtensionInterface, CommandRunnerInterface
{
    use CommandRunnerTrait;

    private bool $isBooted = false;

    private array $commands = [];

    protected static string $name;

    protected InputResolverInterface $resolver;

    abstract function getCommandTag(): string;

    abstract function prepareContainer(ContainerBuilder $container): void;

    public function __construct(InputResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public static function create(ContainerBuilder $container): static
    {
        $instance = new static($container->get(InputResolver::class));
        $instance->prepareContainer($container);
        $instance->loadCommand($container);

        return $instance;
    }

    public function executeCommand(CommandInterface $command, InputInterface $input): void
    {
        $args = $this->resolver->resolve($command, $input);

        $command($args, new ConsoleOutput());
    }

    public function loadCommand(ContainerBuilder $container): void
    {
        dd($this->getCommandTag());
        dd($container->findTaggedServiceIds($this->getCommandTag()));
        foreach ($container->findTaggedServiceIds($this->getCommandTag()) as $key => $command) {
            /** @phpstan-ignore-next-line */
            $this->addCommand($container->get($key));
        }
    }

    public function boot(): void
    {
        $this->isBooted = true;

        if (empty($this->commands)) {
            throw new \Exception(sprintf('Extension %s has no commands', static::class));
        }
    }

    final public function isBooted(): bool
    {
        return $this->isBooted;
    }

    final public function getCommands(): array
    {
        return $this->commands;
    }

    final public static function getName(): string
    {
        if ('' === static::$name) {
            throw new \Exception(sprintf('Extension %s has no name', static::class));
        }

        return static::$name;
    }

    public function run(array $args): void
    {
        $this->boot();

        if (!$this->hasCommandName($args[0])){
            throw new \Exception(sprintf('Command %s not found', $args[0]));
        }

        $this->executeCommand($this->getCommand($args[0]), new Input($args));
    }
}
