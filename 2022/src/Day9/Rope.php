<?php

declare(strict_types=1);

namespace Ikari7789\Adventofcode\Year2022\Day9;

class Rope
{
    public $visited = [];

    public function __construct(
        public array $knots = []
    ) {
        $this->visited[] = new Knot(0, 0);
    }

    public function drawVisited(): void
    {
        $xMin = 0;
        $xMax = 0;
        $yMin = 0;
        $yMax = 0;

        foreach ($this->visited as $knot) {
            $xMin = min($xMin, $knot->x);
            $xMax = max($xMax, $knot->x);
            $yMin = min($yMin, $knot->y);
            $yMax = max($yMax, $knot->y);
        }

        $grid = array_fill_keys(range($yMin, $yMax), array_fill_keys(range($xMin, $xMax), '.'));

        foreach ($this->visited as $knot) {
            $grid[$knot->y][$knot->x] = '#';
        }

        $grid[0][0] = 's';

        for ($row = $yMax; $row >= $yMin; $row--) {
            for ($column = $xMin; $column <= $xMax; $column++) {
                echo $grid[$row][$column];
            }
            echo PHP_EOL;
        }
    }

    public function moveHead(Direction $direction, int $steps): void
    {
        match ($direction) {
            Direction::Up => $this->moveUp($steps),
            Direction::Down => $this->moveDown($steps),
            Direction::Left => $this->moveLeft($steps),
            Direction::Right => $this->moveRight($steps),
        };

        if (DEBUG) {
            printf(
                '%s %2d : ' . implode(',', array_fill(0, count($this->knots), '%s')) . PHP_EOL,
                $direction->value,
                $steps,
                ...$this->knots
            );
        }
    }

    protected function moveUp(int $steps): void
    {
        for ($step = 0; $step < $steps; $step++) {
            $this->knots[0]->y++;

            $this->moveTail();
        }
    }

    protected function moveDown(int $steps): void
    {
        for ($step = 0; $step < $steps; $step++) {
            $this->knots[0]->y--;

            $this->moveTail();
        }
    }

    protected function moveLeft(int $steps): void
    {
        for ($step = 0; $step < $steps; $step++) {
            $this->knots[0]->x--;

            $this->moveTail();
        }
    }

    protected function moveRight(int $steps): void
    {
        for ($step = 0; $step < $steps; $step++) {
            $this->knots[0]->x++;

            $this->moveTail();
        }
    }

    protected function follow(int $head, int $tail): void
    {
        $xDiff = $this->knots[$head]->x - $this->knots[$tail]->x;
        $yDiff = $this->knots[$head]->y - $this->knots[$tail]->y;

        // Up/down
        if (abs($xDiff) === 2) {
            $this->knots[$tail]->x += $xDiff / 2;

            // Diagonal
            if (abs($yDiff) === 1) {
                $this->knots[$tail]->y += $yDiff;
            }

            if ($tail === count($this->knots) - 1) {
                $this->visited[] = new Knot($this->knots[$tail]->x, $this->knots[$tail]->y);
            }
        }

        // Left/right
        if (abs($yDiff) === 2) {
            $this->knots[$tail]->y += $yDiff / 2;

            // Diagonal
            if (abs($xDiff) === 1) {
                $this->knots[$tail]->x += $xDiff;
            }

            if ($tail === count($this->knots) - 1) {
                $this->visited[] = new Knot($this->knots[$tail]->x, $this->knots[$tail]->y);
            }
        }
    }

    protected function moveTail(): void
    {
        for ($index = 0; $index < count($this->knots) - 1; $index++) {
            $this->follow($index, $index + 1);
        }

        return;

        $xDiff = $this->knots[0]->x - $this->knots[1]->x;
        $yDiff = $this->knots[0]->y - $this->knots[1]->y;

        // Up/down
        if (abs($xDiff) === 2) {
            $this->knots[1]->x += $xDiff / 2;

            // Diagonal
            if (abs($yDiff) === 1) {
                $this->knots[1]->y += $yDiff;
            }

            $this->visited[] = new Knot($this->knots[1]->x, $this->knots[1]->y);
        }

        // Left/right
        if (abs($yDiff) === 2) {
            $this->knots[1]->y += $yDiff / 2;

            // Diagonal
            if (abs($xDiff) === 1) {
                $this->knots[1]->x += $xDiff;
            }

            $this->visited[] = new Knot($this->knots[1]->x, $this->knots[1]->y);
        }
    }
}
