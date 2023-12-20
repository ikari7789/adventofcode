<?php

declare(strict_types=1);

class Workflow implements Stringable
{
    public function __construct(
        readonly private string $name,
        readonly private array $rules,
        readonly private string $fallback,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function rules(): array
    {
        return $this->rules;
    }

    public function fallback(): string
    {
        return $this->fallback;
    }

    public function __toString(): string
    {
        $string = '';

        $string .= $this->name();
        $string .= '{';
        $string .= implode(',', $this->rules());
        $string .= ',';
        $string .= $this->fallback();
        $string .= '}';

        return $string;
    }
}
