<?php

namespace Pnl\Console;

use Pnl\App\CommandInterface;
use Pnl\Console\Input\ArgumentBag;
use Pnl\Console\Input\ArgumentDefinition;
use Pnl\Console\Input\ArgumentType;
use Pnl\Console\Input\InputBag;
use Pnl\Console\Input\InputInterface;

class InputResolver implements InputResolverInterface
{
    public function resolve(CommandInterface $command, InputInterface $arguments): InputInterface
    {
        $args = $command::getArguments();

        $resolvedBag = new InputBag();

        foreach ($args->getAll() as $argument) {
            if ($argument->isRequired() && $argument->isNameless() && $arguments->haveNameless()) {
                $resolvedBag->addArgument($argument->getName(), $arguments->getNameless(), true);

                continue;
            } else if ($argument->isNameless() && $arguments->haveNameless()) {
                $resolvedBag->addArgument($argument->getName(), $arguments->getNameless(), true);

                continue;
            }

            if ($argument->isRequired() && !$arguments->hasArgument($argument->getName())) {
                throw new \Exception(sprintf('The %s argument is required', $argument->getName()));
            }

            if (!$arguments->hasArgument($argument->getName())) {
                $resolvedBag->addArgument($argument->getName(), $argument->getDefault());
            }
        }

        foreach ($arguments->getAllArguments() as $name => $value) {
            $this->validateArgument($name, $value, $command::getArguments());
            $resolvedBag->addArgument($name, $value);
        }

        return $resolvedBag;
    }

    private function validateArgument(string $name, mixed $value, ArgumentBag $bag): true
    {
        if (!$bag->has($name)) {
            $commands = array_map(fn ($arg) => $arg->getName(), $bag->getAll());

            throw new \InvalidArgumentException(sprintf('The %s option does not exist available option are : %s', $name, join(' ', $commands)));
        }

        if (!$this->validateType($value, $bag->get($name))) {
            $type = $bag->get($name)->getType();

            /** @phpstan-ignore-next-line */
            throw new \InvalidArgumentException(sprintf('The %s option only supports %s type', $name, $type->value));
        }

        return true;
    }

    private function validateType(mixed $value, ArgumentDefinition $definition): bool
    {
        if (null == $definition->getType()) {
            return true;
        }

        switch ($definition->getType()) {
            case ArgumentType::INT:
                return is_numeric($value);
            case ArgumentType::FLOAT:
                return is_float($value);
            case ArgumentType::STRING:
                return is_string($value);
            case ArgumentType::BOOLEAN:
                return true;
            default:
                return false;
        }
    }
}
