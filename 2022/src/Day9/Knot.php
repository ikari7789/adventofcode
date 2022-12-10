<?php

declare(strict_types=1);

namespace Ikari7789\Adventofcode\Year2022\Day9;

class Knot
{
    public function __construct(
        public int $x,
        public int $y
    ) {
    }

    public function __toString(): string
    {
        return sprintf('(%4d, %4d)', $this->x, $this->y);
    }
}
