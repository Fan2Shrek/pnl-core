<?php

namespace Pnl\Runtime;

use Pnl\Application;
use Pnl\Runtime\Resolver\BaseResolver;
use Pnl\Runtime\Resolver\ResolverInterface;
use Pnl\Runtime\Runner\AppRunner;
use Pnl\Runtime\Runner\RunnerInterface;

class PnlRuntime implements RuntimeInterface
{
    private array $additionals = [];

    public function __construct()
    {
        $this->parseArgs(\func_get_args());
    }

    private function parseArgs(array $args): void
    {
        foreach ($args as $arg) {
            if (\is_array($arg)) {
                $this->parseArgs($arg);
            }

            if (\array_key_exists($arg::class, $this->additionals)) {
                throw new \InvalidArgumentException(sprintf('Cannot handle arg %s of type %s', $arg, $arg::class));
            }

            $this->additionals[$arg::class] = $arg;
        }
    }

    public function getResolver(callable $callable): ResolverInterface
    {
        $params = (new \ReflectionFunction($callable))->getParameters();

        $args = [];

        foreach ($params as $param) {
            $args[] =  $this->resolveArg($param);
        }

        return new BaseResolver($callable(...), $args);
    }

    public function getRunner(mixed $obj): RunnerInterface
    {
        if ($obj instanceof Application) {
            return new AppRunner($obj);
        }

        throw new \InvalidArgumentException(sprintf('Cannot run object %s of type %s', $obj, $obj::class));
    }

    private function resolveArg(\ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if (null === $type) {
            throw new \InvalidArgumentException(\sprintf('Cannot resolve $%s', $parameter->getName()));
        }

        switch ($parameter->getName()) {
            case 'context':
                return $_SERVER + $_ENV;

            case 'argv':
                return $_SERVER['argv'];

            case 'classLoader':
                if (isset($this->additionals[$parameter->getType()->getName()])) {
                    return $this->additionals[$parameter->getType()->getName()];
                };

        };

        throw new \InvalidArgumentException(sprintf('Cannot resolve %s', $parameter->getName()));
    }
}
