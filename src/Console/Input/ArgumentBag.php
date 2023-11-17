<?php

namespace Pnl\Console\Input;

use Traversable;

class ArgumentBag
{
    /**
     * @var ArgumentDefinition[]
     */
    private array $arguments = [];

    private ?ArgumentDefinition $nameless = null;

    public function addArgument(ArgumentDefinition $arg): static
    {
        if ($arg->isNameless()) {
            if (null !== $this->nameless) {
                throw new \InvalidArgumentException('Nameless argument already defined');
            }

            $this->nameless = $arg;
        }

        $this->arguments[$arg->getName()] = $arg;

        return $this;
    }

    public function add(string $name, bool $required = false, ?string $description = null, ?ArgumentType $type = null, mixed $default = null, bool $nameless = false): static
    {
        $this->addArgument(new ArgumentDefinition($name, $required, $description, $type, $default, $nameless));

        return $this;
    }

    public function get(string $name): ArgumentDefinition
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('Argument %s does not exist', $name));
        }

        return $this->arguments[$name];
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->arguments);
    }

    /**
     * @return ArgumentDefinition[]
     */
    public function getAll(): array
    {
        return $this->arguments;
    }

    /**
     * @return Traversable<ArgumentDefinition>
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->arguments);
    }

    /**
     * @return ArgumentDefinition[]
     */
    public function getAllRequire(): array
    {
        return array_filter($this->arguments, fn ($arg) => $arg->isRequired());
    }

    public function getNameless(): ?ArgumentDefinition
    {
        return $this->nameless;
    }
}
