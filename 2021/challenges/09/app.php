<?php

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('UP', 0);
define('RIGHT', 1);
define('DOWN', 2);
define('LEFT', 3);

function isTopLeftCorner(int $height, int $width, int $row, int $column): bool
{
    if (isTopWall($height, $width, $row) && isLeftWall($height, $width, $column)) {
        return true;
    }

    return false;
}

function isTopRightCorner(int $height, int $width, int $row, int $column): bool
{
    if (isTopWall($height, $width, $row) && isRightWall($height, $width, $column)) {
        return true;
    }

    return false;
}

function isBottomLeftCorner(int $height, int $width, int $row, int $column): bool
{
    if (isBottomWall($height, $width, $row) && isLeftWall($height, $width, $column)) {
        return true;
    }

    return false;
}

function isBottomRightCorner(int $height, int $width, int $row, int $column): bool
{
    if (isBottomWall($height, $width, $row) && isRightWall($height, $width, $column)) {
        return true;
    }

    return false;
}

function isTopWall(int $height, int $width, $row): bool
{
    if (DEBUG) {
        printf(
            'Checking is top wall:    %3d ==> %d' . PHP_EOL,
            $row,
            0,
        );
    }

    if ($row === 0) {
        return true;
    }

    return false;
}

function isBottomWall(int $height, int $width, $row): bool
{
    if (DEBUG) {
        printf(
            'Checking is bottom wall: %3d ==> %d' . PHP_EOL,
            $row,
            $height,
        );
    }

    if ($row === $height) {
        return true;
    }

    return false;
}

function isLeftWall(int $height, int $width, $column): bool
{
    if (DEBUG) {
        printf(
            'Checking is left wall:   %3d ==> %d' . PHP_EOL,
            $column,
            0,
        );
    }

    if ($column === 0) {
        return true;
    }

    return false;
}

function isRightWall(int $height, int $width, $column): bool
{
    if (DEBUG) {
        printf(
            'Checking is right wall:  %3d ==> %d' . PHP_EOL,
            $column,
            $width,
        );
    }

    if ($column === $width) {
        return true;
    }

    return false;
}

function checkTop(array $map, int $row, int $column): bool
{
    if (DEBUG) {
        printf(
            'Checking top:    %3d,%3d < %3d,%3d' . PHP_EOL,
            $row,
            $column,
            $row - 1,
            $column
        );
    }

    if ($row - 1 < 0) {
        return false;
    }

    if ($map[$row][$column] < $map[$row - 1][$column]) {
        return true;
    }

    return false;
}

function checkRight(array $map, int $row, int $column): bool
{
    if (DEBUG) {
        printf(
            'Checking right:  %3d,%3d < %3d,%3d' . PHP_EOL,
            $row,
            $column,
            $row,
            $column + 1
        );
    }

    if ($column + 1 >= count($map[$row])) {
        return false;
    }

    if ($map[$row][$column] < $map[$row][$column + 1]) {
        return true;
    }

    return false;
}

function checkBottom(array $map, int $row, int $column): bool
{
    if (DEBUG) {
        printf(
            'Checking bottom: %3d,%3d < %3d,%3d' . PHP_EOL,
            $row,
            $column,
            $row + 1,
            $column
        );
    }

    if ($row + 1 >= count($map)) {
        return false;
    }

    if ($map[$row][$column] < $map[$row + 1][$column]) {
        return true;
    }

    return false;
}

function checkLeft(array $map, int $row, int $column): bool
{
    if (DEBUG) {
        printf(
            'Checking left:   %3d,%3d < %3d,%3d' . PHP_EOL,
            $row,
            $column,
            $row,
            $column - 1
        );
    }

    if ($column - 1 < 0) {
        return false;
    }

    if ($map[$row][$column] < $map[$row][$column - 1]) {
        return true;
    }

    return false;
}

$input = new SplFileObject(INPUT_FILE);

$map = [];
while (! $input->eof() && $line = trim($input->fgets())) {
    $map[] = array_map(function ($value) {
        return (int) $value;
    }, str_split($line));
}

// Making assumption that all rows are equal in length
$height = count($map) - 1;
$width  = count($map[0]) - 1;

$lowPoints = [];
for ($row = 0; $row < count($map); ++$row) {
    for ($column = 0; $column < count($map[$row]); ++$column) {
        if (DEBUG) {
            printf(
                'Checking coordinates: %3d,%3d' . PHP_EOL,
                $row,
                $column
            );
        }

        //
        // Check if corner
        //
        if (isTopLeftCorner($height, $width, $row, $column)) {
            if (DEBUG) {
                echo 'Is top left corner !!' . PHP_EOL;
            }

            if (
                checkRight($map, $row, $column)
                && checkBottom($map, $row, $column)
            ) {
                $lowPoints[] = [$row, $column];
            }

            continue;
        }

        if (isTopRightCorner($height, $width, $row, $column)) {
            if (DEBUG) {
                echo 'Is top right corner !!' . PHP_EOL;
            }

            if (
                checkLeft($map, $row, $column)
                && checkBottom($map, $row, $column)
            ) {
                $lowPoints[] = [$row, $column];
            }

            continue;
        }

        if (isBottomLeftCorner($height, $width, $row, $column)) {
            if (DEBUG) {
                echo 'Is bottom left corner !!' . PHP_EOL;
            }

            if (
                checkTop($map, $row, $column)
                && checkRight($map, $row, $column)
            ) {
                $lowPoints[] = [$row, $column];
            }

            continue;
        }

        if (isBottomRightCorner($height, $width, $row, $column)) {
            if (DEBUG) {
                echo 'Is top right corner !!' . PHP_EOL;
            }

            if (
                checkTop($map, $row, $column)
                && checkLeft($map, $row, $column)
            ) {
                $lowPoints[] = [$row, $column];
            }

            continue;
        }

        //
        // Check if wall
        //
        if (isTopWall($height, $width, $row)) {
            if (DEBUG) {
                echo 'Is top wall !!' . PHP_EOL;
            }

            if (
                checkLeft($map, $row, $column)
                && checkBottom($map, $row, $column)
                && checkRight($map, $row, $column)
            ) {
                $lowPoints[] = [$row, $column];
            }

            continue;
        }

        if (isLeftWall($height, $width, $column)) {
            if (DEBUG) {
                echo 'Is left wall !!' . PHP_EOL;
            }

            if (
                checkTop($map, $row, $column)
                && checkRight($map, $row, $column)
                && checkBottom($map, $row, $column)
            ) {
                $lowPoints[] = [$row, $column];
            }

            continue;
        }

        if (isBottomWall($height, $width, $row)) {
            if (DEBUG) {
                echo 'Is bottom wall !!' . PHP_EOL;
            }

            if (
                checkLeft($map, $row, $column)
                && checkTop($map, $row, $column)
                && checkRight($map, $row, $column)
            ) {
                $lowPoints[] = [$row, $column];
            }

            continue;
        }

        if (isRightWall($height, $width, $column)) {
            if (DEBUG) {
                echo 'Is right wall !!' . PHP_EOL;
            }

            if (
                checkTop($map, $row, $column)
                && checkLeft($map, $row, $column)
                && checkBottom($map, $row, $column)
            ) {
                $lowPoints[] = [$row, $column];
            }

            continue;
        }

        if (DEBUG) {
            echo 'Is inner space !!' . PHP_EOL;
        }

        //
        // Check inner space
        //
        if (
            checkTop($map, $row, $column)
            && checkRight($map, $row, $column)
            && checkBottom($map, $row, $column)
            && checkLeft($map, $row, $column)
        ) {
            $lowPoints[] = [$row, $column];
        }
    }
}

$riskLevel = 0;
foreach ($lowPoints as $coordinate) {
    if (DEBUG) {
        printf(
            'Low point: %3d, %3d (%d)' . PHP_EOL,
            $coordinate[0],
            $coordinate[1],
            $map[$coordinate[0]][$coordinate[1]]
        );
    }

    $riskLevel += 1 + $map[$coordinate[0]][$coordinate[1]];
}

printf('Risk level: %3d' . PHP_EOL, $riskLevel);

function determineBasins(array $map): array
{
    // Initialize blank map of all 0s
    $basins = array_fill(
        0,
        count($map),
        array_fill(
            0,
            count($map[0]),
            0
        )
    );

    // If any point has a higher number next to it, mark it as 1
    // Points with any neighboring 1 can be considered a basin
    for ($row = 0; $row < count($map); ++$row) {
        for ($column = 0; $column < count($map[$row]); ++$column) {
            if (
                checkTop($map, $row, $column)
                || checkRight($map, $row, $column)
                || checkBottom($map, $row, $column)
                || checkLeft($map, $row, $column)
            ) {
                $basins[$row][$column] = 1;
            }
        }
    }

    return $basins;
}

function printMap(array $map): void
{
    for ($row = 0; $row < count($map); ++$row) {
        for ($column = 0; $column < count($map[$row]); ++$column) {
            echo $map[$row][$column];
        }
        echo PHP_EOL;
    }
}

function calculateBasinSizes(array $map): array
{
    $basins = [];

    for ($row = 0; $row < count($map); ++$row) {
        for ($column = 0; $column < count($map[$row]); ++$column) {
            $basins[] = calculateBasinSize($map, $row, $column);
        }
    }

    sort($basins);

    $basins = array_filter($basins, function ($value) {
        return $value !== 0;
    });

    return $basins;
}

function calculateBasinSize(array &$map, int $row, int $column): int
{
    $size = 0;

    if (isset($map[$row][$column]) && $map[$row][$column] === 1) {
        $map[$row][$column] = 0;
        ++$size;

        $size += calculateBasinSize($map, $row - 1, $column);
        $size += calculateBasinSize($map, $row, $column + 1);
        $size += calculateBasinSize($map, $row + 1, $column);
        $size += calculateBasinSize($map, $row, $column - 1);
    }

    return $size;
}

$basins = determineBasins($map);
if (DEBUG) {
    printMap($basins);
}

$basinSizes = calculateBasinSizes($basins);

$numberOfBasins = count($basinSizes);
$numberOfBasinsToMultiply = 3;
if ($numberOfBasins < $numberOfBasinsToMultiply) {
    $numberOfBasinsToMultiply = $numberOfBasins;
}
$largestBasins = array_slice($basinSizes, $numberOfBasins - $numberOfBasinsToMultiply);

if (DEBUG) {
    print_r($largestBasins);
}
$sumOfBasinSizes = array_reduce($largestBasins, function ($carry, $value) {
    return $carry * $value;
}, 1);

printf('Multiplicative sum of 3 largest basins: %d' . PHP_EOL, $sumOfBasinSizes);
