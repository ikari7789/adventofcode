<?php

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('HEIGHT', 895);
define('WIDTH', 1311);
define('X_AXIS', 'x');
define('Y_AXIS', 'y');

$grid       = array_fill(0, HEIGHT, array_fill(0, WIDTH, '.'));
$foldAlongs = [];

$input = new SplFileObject(INPUT_FILE);

while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    if (preg_match('/^\d+,\d+$/', $line)) {
        [
            $y,
            $x,
        ] = explode(',', $line);
        $grid[$x][$y] = '#';
    }

    if (preg_match('/^fold along (x|y)=(\d+)$/', $line, $matches)) {
        $foldAlongs[] = [$matches[1], $matches[2]];
    }
}

function printGrid(array $grid): void
{
    foreach ($grid as $row => $columns) {
        foreach ($columns as $column) {
            echo $column;
        }
        echo PHP_EOL;
    }
}

function fold($grid, $axis, $line): array
{
    $mirror = [];

    switch ($axis) {
        case X_AXIS:
            $newGrid = [];

            for ($row = 0; $row < count($grid); ++$row) {
                $newGrid[$row] = array_slice($grid[$row], 0, $line);
                $mirror[$row]  = array_reverse(array_slice($grid[$row], $line + 1));
            }

            $grid = $newGrid;

            break;

        case Y_AXIS:
            $mirror = array_reverse(array_slice($grid, $line + 1));
            $grid   = array_slice($grid, 0, $line);

            break;
    }

    for ($row = 0; $row < count($grid); ++$row) {
        for ($column = 0; $column < count($grid[$row]); ++$column) {
            if ($mirror[$row][$column] === '#') {
                $grid[$row][$column] = $mirror[$row][$column];
            }
        }
    }

    return $grid;
}

function countDots(array $grid): int
{
    $dots = 0;

    foreach ($grid as $columns) {
        foreach ($columns as $column) {
            if ($column === '#') {
                ++$dots;
            }
        }
    }

    return $dots;
}

// dump($grid);
// dump($foldAlong);
printGrid($grid);
echo PHP_EOL;

$foldCount = 0;
$dotsAfterFirstFold = 0;
foreach ($foldAlongs as $foldAlong) {
    [
        $axis,
        $line,
    ] = $foldAlong;

    ++$foldCount;

    $grid = fold($grid, $axis, $line);

    printf('Fold %d' . PHP_EOL, $foldCount);
    printGrid($grid);
    echo PHP_EOL;

    if ($foldCount === 1) {
        $dotsAfterFirstFold = countDots($grid);
    }
}

printf('Dots after first fold: %d' . PHP_EOL, $dotsAfterFirstFold);
