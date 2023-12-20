<?php

declare(strict_types=1);

require_once 'Part.php';

class Parts implements Stringable
{
    private array $parts;

    public function parts(): array
    {
        return $this->parts;
    }

    public function addPart(Part $part): void
    {
        $this->parts[] = $part;
    }

    public function __toString(): string
    {
        $string = '';

        $string .= implode(PHP_EOL, $this->parts);

        return $string;
    }
}
