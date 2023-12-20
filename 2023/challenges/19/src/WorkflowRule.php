<?php

declare(strict_types=1);

require_once 'Comparator.php';
require_once 'PartType.php';

class WorkflowRule implements Stringable
{
    public function __construct(
        readonly private PartType $partType,
        readonly private Comparator $comparator,
        readonly private int $value,
        readonly private string $fallback,
    ) {}

    public function partType(): PartType
    {
        return $this->partType;
    }

    public function comparator(): Comparator
    {
        return $this->comparator;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function fallback(): string
    {
        return $this->fallback;
    }

    public function __toString(): string
    {
        $string = '';

        $string .= $this->partType()->value;
        $string .= $this->comparator()->value;
        $string .= $this->value();
        $string .= ':';
        $string .= $this->fallback();

        return $string;
    }
}
