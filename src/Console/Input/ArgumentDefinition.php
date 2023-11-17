<?php

namespace Pnl\Console\Input;

class ArgumentDefinition
{
    public function __construct(
        private string $name,
        private bool $required = false,
        private ?string $description = null,
        private ?ArgumentType $type = null,
        private mixed $default = null,
        private bool $nameless = false,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function setDefault(mixed $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function getType(): ?ArgumentType
    {
        return $this->type;
    }

    public function setType(?ArgumentType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isNameless(): bool
    {
        return $this->nameless;
    }

    public function setNameless(bool $nameless): static
    {
        $this->nameless = $nameless;

        return $this;
    }
}
