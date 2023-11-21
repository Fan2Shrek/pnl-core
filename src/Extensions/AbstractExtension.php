<?php

namespace Pnl\Extensions;
use Pnl\App\CommandInterface;
use Pnl\App\CommandRunnerInterface;
use Pnl\App\CommandRunnerTrait;
use Pnl\Console\InputResolver;
use Pnl\Console\InputResolverInterface;
use Pnl\Console\Input\Input;
use Pnl\Console\Input\InputInterface;
use Pnl\Runtime\Resolver\ResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractExtension implements ExtensionInterface, CommandRunnerInterface
{
    use CommandRunnerTrait;

    private bool $isBooted = false;

    private array $commands = [];

    protected static string $name ;

    protected InputResolverInterface $resolver;

    public function __construct(InputResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public static function create(ContainerInterface $container): static
    {
        return new static($container->get(InputResolver::class));
    }

    public function executeCommand(CommandInterface $command, InputInterface $input): void
    {

    }

    public function boot(): void
    {
        $this->isBooted = true;

        if (empty($this->commands)) {
            $this->loadCommand();
        }
    }

    protected function loadCommand(): void
    {
        $this->commands = [];
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
