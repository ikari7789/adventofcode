<?php
declare(strict_types=1);

/**
 * Credit to https://www.reddit.com/r/adventofcode/comments/rca6vp/comment/hnw3huo/?utm_source=reddit&utm_medium=web2x&context=3
 */

$input = new SplFileObject('input.txt');

$heights = [];
while($row = $input->fgets())
{
    $heights[] = str_split(trim($row));
}

$cave = new Cave($heights);
$basins = $cave->findBasins();
usort($basins, fn(array $a, array $b) => sizeof($b) - sizeof($a));
echo sizeof($basins[0]) * sizeof($basins[1]) * sizeof($basins[2]), "\n";

class Cave
{
    public function __construct(
        private array $cave
    ) {}

    public function getLowPointIndexes(): array
    {
        $result = [];
        foreach($this->cave as $r => $row)
        {
            foreach($row as $c => $height)
            {
                if($this->isLowPoint($r, $c))
                {
                    $result[] = [$r, $c];
                }
            }
        }
        return $result;
    }

    public function getLowPoints(): array
    {
        return array_map(fn(array $point) => $this->grid[$point[0]][$point[1]], $this->getLowPointIndexes());
    }

    public function findBasins(): array
    {
        $basins = [];
        $visited = [];
        foreach($this->getLowPointIndexes() as $lowPoint)
        {
            $basins[] = $this->findBasin($lowPoint[0], $lowPoint[1], $visited);
        }
        return $basins;
    }

    public function findBasin(int $row, int $column, array &$visited): array
    {
        $lowPoint = $this->cave[$row][$column];
        if(array_key_exists("$row, $column", $visited) || $lowPoint === 9)
        {
            return [];
        }
        $visited["$row, $column"] = true;
        $neighbors = $this->getNeighborsOf($row, $column);
        $basin = [$lowPoint];
        foreach($neighbors as $n)
        {
            $neighborHeight = $this->cave[$n[0]][$n[1]];
            if($neighborHeight !== '9' && $neighborHeight >= $lowPoint)
            {
                $basin = array_merge($basin, $this->findBasin($n[0], $n[1], $visited));
            }
        }
        return $basin;
    }

    private function isLowPoint(int $row, int $column): bool
    {
        $neighbors = $this->getNeighborsOf($row, $column);
        foreach($neighbors as [$r, $c])
        {
            if($this->cave[$row][$column] >= $this->cave[$r][$c])
            {
                return false;
            };
        }
        return true;
    }

    private function getNeighborsOf(int $row, int $column): array
    {
        return array_filter([
            [$row - 1, $column],
            [$row + 1, $column],
            [$row, $column - 1],
            [$row, $column + 1],
        ], fn($point) => $this->isWithinBounds($point[0], $point[1]));
    }

    private function isWithinBounds(int $row, int $column): bool
    {
        return 0 <= $row && $row < sizeof($this->cave) && 0 <= $column && $column < sizeof($this->cave[0]);
    }
}
