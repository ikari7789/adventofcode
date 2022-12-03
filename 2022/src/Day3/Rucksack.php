<?php

declare(strict_types=1);

namespace Ikari7789\Adventofcode\Year2022\Day3;

class Rucksack
{
    /** @var array<string, int> */
    public const PRIORITIES = [
        'a' => 1,
        'b' => 2,
        'c' => 3,
        'd' => 4,
        'e' => 5,
        'f' => 6,
        'g' => 7,
        'h' => 8,
        'i' => 9,
        'j' => 10,
        'k' => 11,
        'l' => 12,
        'm' => 13,
        'n' => 14,
        'o' => 15,
        'p' => 16,
        'q' => 17,
        'r' => 18,
        's' => 19,
        't' => 20,
        'u' => 21,
        'v' => 22,
        'w' => 23,
        'x' => 24,
        'y' => 25,
        'z' => 26,
        'A' => 27,
        'B' => 28,
        'C' => 29,
        'D' => 30,
        'E' => 31,
        'F' => 32,
        'G' => 33,
        'H' => 34,
        'I' => 35,
        'J' => 36,
        'K' => 37,
        'L' => 38,
        'M' => 39,
        'N' => 40,
        'O' => 41,
        'P' => 42,
        'Q' => 43,
        'R' => 44,
        'S' => 45,
        'T' => 46,
        'U' => 47,
        'V' => 48,
        'W' => 49,
        'X' => 50,
        'Y' => 51,
        'Z' => 52,
    ];

    /**
     * @var array<array<int, string>
     */
    public array $rucksackCompartments = [];

    public function __construct(
        public string $items,
        public int $compartmentCount = 1
    ) {
        $itemsPerCompartment = strlen($items) / $compartmentCount;

        $position = 0;
        for ($compartment = 0; $compartment < $compartmentCount; $compartment++) {
            array_push($this->rucksackCompartments, str_split(substr($items, $position, $itemsPerCompartment)));
            $position += $itemsPerCompartment;
        }
    }

    public function compartmentIntersection(): int
    {
        $item = array_values(array_unique(array_intersect(...$this->rucksackCompartments)))[0];
        return $this->itemToPriority($item);
    }

    private function itemToPriority(string $item): int
    {
        return static::PRIORITIES[$item];
    }
}
