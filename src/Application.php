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
use Pnl\Console\Output\ANSI\BackgroundColor;
use Pnl\Console\Output\ANSI\Style as AnsiStyle;
use Pnl\Console\Output\ANSI\TextColors;
use Pnl\Console\Output\ConsoleOutput;
use Pnl\Console\Output\Style\Style;
use Pnl\Extensions\AbstractExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Application implements CommandRunnerInterface
{
    use CommandRunnerTrait;

    private string $version = '0.0.1';

    private ContainerBuilder $container;

    private ComposerContext $composerContext;

    /** @phpstan-ignore-next-line */
    private array $context = [];

    private string $appRoot;

    /** @var AbstractExtension[] */
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
    public function run(array $args = []): int
    {
        $this->boot();

        $exceptionClosuer = function (\Throwable $e) {
            $this->handleException($e);
        };

        set_exception_handler($exceptionClosuer);

        if (empty($this->commandList)) {
            throw new \Exception('No commands found');
        }

        $commandName = !empty($args) ? array_shift($args) : 'help';

        if (in_array($commandName, ['-v', '--version'])) {
            echo sprintf("PNL version: %s\n", $this->version);

            return 0;
        }

        if (!$this->hasCommandName($commandName) && !$this->hasExtension($commandName)) {
            throw new CommandNotFoundException(sprintf('Command %s not found', $commandName));
        }

        try {
            if ($this->hasExtension($commandName)) {
                $this->runExtension($commandName, $args);
            } else {
                $this->executeCommand($this->getCommand($commandName), new Input($args));
            }

            $exitCode = 0;
        } catch (\Exception $e) {
            $exitCode = $e->getCode();

            $this->handleException($e);

            if (0 === $exitCode || is_string($exitCode)) {
                $exitCode = 1;
            }
        }

        return $exitCode;
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

        $loader = new YamlFileLoader($builder, new FileLocator($this->appRoot . '/config'));
        $loader->load('services.yaml');

        $builder->set($this::class, $this);
        $builder->addCompilerPass(new CommandCompiler());

        if (empty($this->extensions)) {
            $this->loadExtensions($builder);
        }

        $builder->compile();

        $this->container = $builder;
    }

    /**
     * @param string[] $args
     */
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

    /**
     * @todo fix background color
     */
    private function handleException(\Throwable $e): void
    {
        $width = (int)exec('tput cols');
        $message = $e->getMessage();

        $lastTrace = $e->getTrace()[0];

        if (strlen($message) < $width) {
            $offset = (int)floor(($width - strlen($message)) / 2);
        } else {
            $offset = 0;
        }

        $errorStyle = new Style(new ConsoleOutput());

        $errorStyle->setBackground(BackgroundColor::RED)
            ->setColor(TextColors::RESET)
            ->setStyle(AnsiStyle::BOLD);

        $errorStyle->start();
        $errorStyle->writeln(sprintf('From: %s:%d', $lastTrace['class'] ?? 'unknown', $lastTrace['line'] ?? "unknown"));
        $errorStyle->writeln("");
        $errorStyle->writeln(str_repeat(' ', $offset) . $e->getMessage());
        $errorStyle->writeln('');
        $errorStyle->writeln('');
    }

    public function getInputResolver(): InputResolverInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->container->get(InputResolver::class);
    }

    private function loadComposerContext(): bool
    {
        if (!file_exists('composer.json')) {
            return false;
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
        if (!file_exists($this->appRoot . 'config/extensions.php')) {
            return;
        }

        $extensions = require $this->appRoot . 'config/extensions.php';

        foreach ($extensions as $extension) {
            $this->addExtension($extension, $container);
        }
    }

    /**
     * @param class-string<AbstractExtension> $extension
     */
    private function addExtension(string $extension, ContainerBuilder $container): void
    {
        $reflection = new \ReflectionClass($extension);

        if (!$reflection->isSubclassOf(AbstractExtension::class)) {
            throw new \Exception(sprintf('Extension %s must extend %s', $extension, AbstractExtension::class));
        }

        $this->extensions[$extension::getName()] = $extension::create($container);
    }

    public function get(string $name): ?string
    {
        return $this->context[$name] ?? null;
    }
}
