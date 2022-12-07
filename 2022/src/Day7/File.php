<?php

declare(strict_types=1);

namespace Ikari7789\Adventofcode\Year2022\Day7;

class File
{
    public function __construct(
        public string $name,
        public int $size,
        public ?Directory $parent = null
    ) { }

    public function size(): int
    {
        return $this->size;
    }

    public function __toString(): string
    {
        return $this->name . ' (file, size=' . $this->size . ')';
    }
}
