<?php

declare(strict_types=1);

namespace Ikari7789\Adventofcode\Year2022\Day1;

class Elf
{
    /**
     * @var array<int>
     */
    public $inventory = [];

    public function totalCalories(): int
    {
        return array_sum($this->inventory);
    }
}
