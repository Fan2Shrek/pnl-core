<?php

namespace Pnl;

use Composer\Autoload\ClassLoader;
use Pnl\App\CommandInterface;
use Pnl\App\CommandRunnerInterface;
use Pnl\App\CommandRunnerTrait;
use Pnl\App\DependencyInjection\CommandCompiler;
use Pnl\App\Exception\CommandNotFoundException;
use Pnl\Composer\ComposerContext;
use Pnl\Console\InputResolver;
use Pnl\Console\InputResolverInterface;
use Pnl\Console\Input\Input;
use Pnl\Console\Input\InputInterface;
use Pnl\Console\Output\ConsoleOutput;
use Pnl\Extensions\AbstractExtension;
use Pnl\PnlPhp\Commands\HelloCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Application implements CommandRunnerInterface
{
    use CommandRunnerTrait;

    private ContainerBuilder $container;

    private ComposerContext $composerContext;

    /** @phpstan-ignore-next-line */
    private array $context = [];

    private string $appRoot;

    private array $extensions = [];

    private bool $isBooted = false;

    /**
     * @var CommandInterface[]
     */
    private array $commandList = [];

    /** @phpstan-ignore-next-line */
    public function __construct(private ClassLoader $classLoader, array $context = [])
    {
        $this->context = $context;
        $this->appRoot = __DIR__ . '/../';
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

        $commandName = array_shift($args);

        if ($this->hasExtension($commandName)){
            $this->runExtension($commandName, $args);

            return;
        }

        if ($this->hasCommandName($commandName)) {
            $this->executeCommand($this->getCommand($commandName), new Input($args));

            return;
        }

        throw new CommandNotFoundException(sprintf('Command %s not found', $commandName));
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

        $loader = new YamlFileLoader($builder, new FileLocator($this->appRoot .'/config'));
        $loader->load('services.yaml');

        $builder->addCompilerPass(new CommandCompiler());

        if (empty($this->extensions)) {
            $this->loadExtensions($builder);
        }

        $builder->compile();

        $this->container = $builder;
    }

    private function runExtension(string $name, array $args = []): void
    {
        $extension = $this->getExtension($name);
        $extension->run($args);
   }

    private function registerCommands(): void
    {
        foreach ($this->container->findTaggedServiceIds('app-command') as $key => $command) {
            /** @phpstan-ignore-next-line */
            $this->addCommand($this->container->get($key));
        }
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

    private function hasExtension(string $extensionName): bool
    {
        return isset($this->extensions[$extensionName]);
    }

    private function getExtension(string $extensionName): AbstractExtension
    {
        $extensionClass = $this->extensions[$extensionName];
        $extensionClass->boot($this->container);

        return $extensionClass;
    }

    private function loadExtensions(ContainerBuilder $container): void
    {
        $extensions = require $this->appRoot . 'config/extensions.php';

        foreach($extensions as $extension) {
            $this->addExtension($extension, $container);
        }
    }

    private function addExtension(string $extension, ContainerBuilder $container): void
    {
        $reflection = new \ReflectionClass($extension);

        if (!$reflection->isSubclassOf(AbstractExtension::class)) {
            throw new \Exception(sprintf('Extension %s must extend %s', $extension, AbstractExtension::class));
        }

        $this->extensions[$extension::getName()] = $extension::create($container);
    }
}
