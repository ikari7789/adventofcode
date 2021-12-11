<?php

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('GRID_SIZE', 10);
define('FLASHING_LIMIT', 9);
define('STEPS_FOR_PART_1', 100);

function printMap(array $map): void
{
    foreach ($map as $row => $columns) {
        foreach ($columns as $column) {
            echo $column;
        }
        echo PHP_EOL;
    }
}

function stepThroughMap(array &$map): int
{
    $totalFlashes = 0;

    for ($row = 0; $row < GRID_SIZE; ++$row) {
        for ($column = 0; $column < GRID_SIZE; ++$column) {
            ++$map[$row][$column];
        }
    }

    do {
        $isFlashing = false;

        // Check for any octopii that need to flash
        for ($row = 0; $row < GRID_SIZE; ++$row) {
            for ($column = 0; $column < GRID_SIZE; ++$column) {
                if ($map[$row][$column] <= FLASHING_LIMIT) {
                    continue;
                }

                // Octopus flashes
                ++$totalFlashes;

                // Energy reset to 0
                $map[$row][$column] = 0;

                // Increase adjacent by 1
                $adjacentCoordinates = [
                    [$row - 1, $column], // top
                    [$row - 1, $column + 1], // top right
                    [$row, $column + 1], // right
                    [$row + 1, $column + 1], // bottom right
                    [$row + 1, $column], // bottom
                    [$row + 1, $column - 1], // bottom left
                    [$row, $column - 1], // left
                    [$row - 1, $column - 1], // top left
                ];

                foreach ($adjacentCoordinates as $coordinates) {
                    $adjacentRow    = $coordinates[0];
                    $adjacentColumn = $coordinates[1];
                    if (isset($map[$adjacentRow][$adjacentColumn]) && $map[$adjacentRow][$adjacentColumn] !== 0) {
                        ++$map[$adjacentRow][$adjacentColumn];

                        if ($map[$adjacentRow][$adjacentColumn] > FLASHING_LIMIT) {
                            $isFlashing = true;
                        }
                    }
                }
            }
        }
    } while ($isFlashing);

    return $totalFlashes;
}

$input = new SplFileObject(INPUT_FILE);

$map = [];
while (! $input->eof() && $line = trim($input->fgets())) {
    $map[] = array_map(function ($value) {
        return (int) $value;
    }, str_split($line));
}

if (DEBUG) {
    echo 'Before any steps:' . PHP_EOL;
    printMap($map);
    echo PHP_EOL;
}

$totalFlashes         = 0;
$totalFlashesThisStep = 0;
$step                 = 0;
while ($totalFlashesThisStep !== GRID_SIZE * GRID_SIZE) {
    ++$step;

    $totalFlashesThisStep = stepThroughMap($map);

    if ($step <= STEPS_FOR_PART_1) {
        $totalFlashes += $totalFlashesThisStep;
    }

    if (DEBUG) {
        printf('After step %d:' . PHP_EOL, $step);
        printf('Total flashes this step: %d' . PHP_EOL, $totalFlashesThisStep);
        printMap($map);
        echo PHP_EOL;
    }
}

printf('Total flashes after 100 steps: %d' . PHP_EOL, $totalFlashes);
printf('All flashed on step %d' . PHP_EOL, $step);
