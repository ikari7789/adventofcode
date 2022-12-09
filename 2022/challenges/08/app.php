<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

function printTrees(array $trees): void
{
    foreach ($trees as $row) {
        foreach ($row as $column) {
            echo $column;
        }

        echo PHP_EOL;
    }
}

function calculateVisibility(array $trees): int
{
    $visibilities = [];

    $rows = count($trees);

    for ($row = 0; $row < $rows; ++$row) {
        $columns = count($trees[$row]);

        // Prefill as visible
        $visibilities[] = array_fill(0, $columns, 1);

        for ($column = 0; $column < $columns; $column++) {
            // Edges are always visible
            if ($row === 0 || $column === 0 || $row === $rows - 1 || $column === $columns - 1) {
                continue;
            }

            $height = $trees[$row][$column];

            // Check up
            $visibleFromUp = 1;
            $currentRow = $row - 1;
            while ($currentRow >= 0) {
                if ($trees[$row][$column] <= $trees[$currentRow][$column]) {
                    $visibleFromUp = 0;

                    break;
                }
                $currentRow--;
            }

            // Check down
            $visibleFromDown = 1;
            $currentRow = $row + 1;
            while ($currentRow < $rows) {
                if ($trees[$row][$column] <= $trees[$currentRow][$column]) {
                    $visibleFromDown = 0;

                    break;
                }
                $currentRow++;
            }

            // Check left
            $visibleFromLeft = 1;
            $currentColumn = $column - 1;
            while ($currentColumn >= 0) {
                if ($trees[$row][$column] <= $trees[$row][$currentColumn]) {
                    $visibleFromLeft = 0;

                    break;
                }
                $currentColumn--;
            }

            // Check right
            $visibleFromRight = 1;
            $currentColumn = $column + 1;
            while ($currentColumn < $columns) {
                if ($trees[$row][$column] <= $trees[$row][$currentColumn]) {
                    $visibleFromRight = 0;

                    break;
                }
                $currentColumn++;
            }

            if (! $visibleFromUp && ! $visibleFromDown && ! $visibleFromLeft && ! $visibleFromRight) {
                $visibilities[$row][$column] = 0;
            }
        }
    }

    printTrees($visibilities);

    $visibleTrees = 0;
    foreach ($visibilities as $row) {
        $visibleTrees += array_sum($row);
    }

    return $visibleTrees;
}

function calculateScenicScore(array $trees): int
{
    $scenicScores = [];

    $rows = count($trees);

    for ($row = 0; $row < $rows; ++$row) {
        $columns = count($trees[$row]);

        // Prefill as 0
        $scenicScores[] = array_fill(0, $columns, 0);

        for ($column = 0; $column < $columns; $column++) {
            $height = $trees[$row][$column];

            printf('Row: %02d, Column: %02d, Height: %d' . PHP_EOL, $row, $column, $height);

            // Check up
            $visibleFromUp = 0;
            $currentRow = $row - 1;
            while ($currentRow >= 0) {
                // if ($trees[$row][$column] >= $trees[$currentRow][$column]) {
                    $visibleFromUp++;
                // }

                if ($trees[$row][$column] <= $trees[$currentRow][$column]) {
                    break;
                }

                $currentRow--;
            }
            printf('Visible from up:    %02d' . PHP_EOL, $visibleFromUp);

            // Check left
            $visibleFromLeft = 0;
            $currentColumn = $column - 1;
            while ($currentColumn >= 0) {
                // if ($trees[$row][$column] >= $trees[$row][$currentColumn]) {
                    $visibleFromLeft++;
                // }

                if ($trees[$row][$column] <= $trees[$row][$currentColumn]) {
                    break;
                }

                $currentColumn--;
            }
            printf('Visible from left:  %02d' . PHP_EOL, $visibleFromLeft);

            // Check down
            $visibleFromDown = 0;
            $currentRow = $row + 1;
            while ($currentRow < $rows) {
                // if ($trees[$row][$column] >= $trees[$currentRow][$column]) {
                    $visibleFromDown++;
                // }

                if ($trees[$row][$column] <= $trees[$currentRow][$column]) {
                    break;
                }

                $currentRow++;
            }
            printf('Visible from down:  %02d' . PHP_EOL, $visibleFromDown);

            // Check right
            $visibleFromRight = 0;
            $currentColumn = $column + 1;
            while ($currentColumn < $columns) {
                // if ($trees[$row][$column] >= $trees[$row][$currentColumn]) {
                    $visibleFromRight++;
                // }

                if ($trees[$row][$column] <= $trees[$row][$currentColumn]) {
                    break;
                }

                $currentColumn++;
            }
            printf('Visible from right: %02d' . PHP_EOL, $visibleFromRight);

            $scenicScores[$row][$column] = $visibleFromUp * $visibleFromDown * $visibleFromLeft * $visibleFromRight;
        }
    }

    printTrees($scenicScores);

    $maxScenicScore = 0;
    foreach ($scenicScores as $row) {
        $maxScenicScore = max($maxScenicScore, max($row));
    }

    return $maxScenicScore;
}

$trees = [];

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);
    $trees[] = str_split($line);
}

$visibleTrees = calculateVisibility($trees);

echo "Visible trees: {$visibleTrees}" . PHP_EOL;

$maxScenicScore = calculateScenicScore($trees);

echo "Max scenic score: {$maxScenicScore}" . PHP_EOL;
