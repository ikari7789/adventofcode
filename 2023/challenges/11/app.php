<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('SPACE', '.');
define('GALAXY', '#');

class Map implements Stringable
{
    private array $graph;

    public function addCoordinate(int $x, int $y, string $marker): void
    {
        if (!isset($graph[$y])) {
            $graph[$y] = [];
        }

        $this->graph[$y][$x] = $marker;
    }

    public function shortestPathBetweenGalaxies(int $expansionMultiplier = 1): array
    {
        // $galaxies = $this->getGalaxies($this->expandGraph($this->graph, $expansionMultiplier));
        $galaxies = $this->getExpandedGalaxies($this->graph, $expansionMultiplier);

        $combinations = [];
        for ($index = 0; $index < count($galaxies); ++$index) {
            for ($innerIndex = $index + 1; $innerIndex < count($galaxies); ++$innerIndex) {
                $pointA = $galaxies[$index];
                $pointB = $galaxies[$innerIndex];
                $distance = $this->distanceBetween($pointA, $pointB);
                $combinations[] = [
                    'point_a' => $pointA,
                    'point_b' => $pointB,
                    'distance' => $distance,
                ];
            }
        }

        return array_map(fn($combination) => $combination['distance'], $combinations);
    }

    private function getGalaxies(array $graph): array
    {
        $galaxies = [];

        for ($y = 0; $y < count($graph); ++$y) {
            for ($x = 0; $x < count($graph[$y]); ++$x) {
                if ($graph[$y][$x] !== GALAXY) {
                    continue;
                }

                $galaxies[] = ['x' => $x, 'y' => $y];
            }
        }

        return $galaxies;
    }

    /**
     * Logic after part 2.
     *
     * Calculate updated graph locations for only galaxies without increasing graph size.
     */
    private function getExpandedGalaxies(array $graph, int $expansionMultiplier = 2): array
    {
        [
            'empty_rows' => $emptyRows,
            'empty_columns' => $emptyColumns,
        ] = $this->emptyRowsAndColumns($this->graph);

        $galaxies = $this->getGalaxies($graph);
        foreach ($galaxies as &$galaxy) {
            $rowMultiplier = 0;
            foreach ($emptyRows as $emptyRow) {
                if ($emptyRow > $galaxy['y']) {
                    break;
                }

                ++$rowMultiplier;
            }

            $columnMultiplier = 0;
            foreach ($emptyColumns as $emptyColumn) {
                if ($emptyColumn > $galaxy['x']) {
                    break;
                }

                ++$columnMultiplier;
            }

            $galaxy['x'] = $galaxy['x'] + ($columnMultiplier * ($expansionMultiplier - 1));
            $galaxy['y'] = $galaxy['y'] + ($rowMultiplier * ($expansionMultiplier - 1));
        }

        return $galaxies;
    }

    /**
     * Logic for part 1.
     *
     * Expand size of graph before retrieving galaxies.
     */
    private function expandGraph(array $graph, int $expansionMultiplier = 2): array
    {
        [
            'empty_rows' => $emptyRows,
            'empty_columns' => $emptyColumns,
        ] = $this->emptyRowsAndColumns($graph);

        $inserted = 0;
        foreach (array_filter($emptyColumns) as $x) {
            for ($y = 0; $y < count($graph); ++$y) {
                array_splice(
                    $graph[$y],
                    $x + $inserted,
                    1,
                    array_fill(0, $expansionMultiplier, SPACE),
                );
            }
            $inserted += $expansionMultiplier - 1;
        }

        $inserted = 0;
        foreach (array_filter($emptyRows) as $y) {
            array_splice(
                $graph,
                $y + $inserted,
                1,
                array_fill(0, $expansionMultiplier, array_fill(0, count($graph[$y]), SPACE))
            );
            $inserted += $expansionMultiplier - 1;
        }

        return $graph;
    }

    private function emptyRowsAndColumns(array $graph): array
    {
        $emptyRows = array_fill(0, count($graph), true);
        $emptyColumns = array_fill(0, count($graph[0]), true);

        $emptyRowTest = array_fill(0, count($graph[0]), SPACE);

        for ($y = 0; $y < count($graph); ++$y) {
            $emptyRows[$y] &= $graph[$y] === $emptyRowTest;

            for ($x = 0; $x < count($graph[$y]); ++$x) {
                $emptyColumns[$x] &= $graph[$y][$x] === SPACE;
            }
        }

        return [
            'empty_rows' => array_keys(array_filter($emptyRows)),
            'empty_columns' => array_keys(array_filter($emptyColumns)),
        ];
    }

    private function distanceBetween(array $pointA, array $pointB): int
    {
        return abs($pointB['y'] - $pointA['y']) + abs($pointB['x'] - $pointA['x']);
    }

    public function __toString(): string
    {
        $string = '';

        for ($y = 0; $y < count($this->graph); ++$y) {
            for ($x = 0; $x < count($this->graph[$y]); ++$x) {
                $string .= $this->graph[$y][$x];
            }
            $string .= PHP_EOL;
        }

        return $string;
    }
}

$map = new Map();

$y = 0;
$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    $coordinates = str_split($line);

    foreach ($coordinates as $x => $marker) {
        $map->addCoordinate($x, $y, $marker);
    }

    ++$y;
}
unset($y);

printf('Sum of shortest paths between galaxies (2x):       %d' . PHP_EOL, array_sum($map->shortestPathBetweenGalaxies(2)));
printf('Sum of shortest paths between galaxies (10x):      %d' . PHP_EOL, array_sum($map->shortestPathBetweenGalaxies(10)));
printf('Sum of shortest paths between galaxies (100x):     %d' . PHP_EOL, array_sum($map->shortestPathBetweenGalaxies(100)));
printf('Sum of shortest paths between galaxies (1000000x): %d' . PHP_EOL, array_sum($map->shortestPathBetweenGalaxies(1000000)));
