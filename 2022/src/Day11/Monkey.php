<?php

declare(strict_types=1);

namespace Ikari7789\Adventofcode\Year2022\Day11;

use GMP;

class Monkey
{
    public int $inspections = 0;

    public function __construct(
        public ?array $items = [],
        public ?array $operation = null,
        public ?GMP $testDivisor = null,
        public ?int $ifTrue = null,
        public ?int $ifFalse = null,
    ) { }
}
