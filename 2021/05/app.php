<?php

define('DEBUG', false);
define('INPUT_FILE', 'input.txt');
define('MAP_SIZE', 1000);
define('HORIZONTAL', 0);
define('VERTICAL', 1);
define('DIAGONAL', 2);

$input = new SplFileObject(INPUT_FILE);

function countOverlappingLines(array $map): int
{
    $overlapping = 0;

    foreach ($map as $row) {
        foreach ($row as $column) {
            $char = $column;

            if ($column === 0) {
                $char = '.';
            }

            if (DEBUG) {
                echo $char;
            }

            if ($column > 1) {
                ++$overlapping;
            }
        }

        if (DEBUG) {
            echo PHP_EOL;
        }
    }

    return $overlapping;
}

// Prefill map with 0
$map = array_fill(0, MAP_SIZE, null);
for ($row = 0; $row < MAP_SIZE; ++$row) {
    $map[$row] = array_fill(0, MAP_SIZE, 0);
}

while (! $input->eof() && $line = trim($input->fgets())) {
    [
        $y1,
        $x1,
        $y2,
        $x2,
    ] = preg_split('/(,| -> )/', $line);

    if (DEBUG) {
        printf('%d,%d -> %d,%d' . PHP_EOL, $x1, $y1, $x2, $y2);
    }

    $direction = HORIZONTAL;

    if ($x1 === $x2) {
        $direction = HORIZONTAL;
        $start = min($y1, $y2);
        $end   = max($y1, $y2);
    }

    if ($y1 === $y2) {
        $direction = VERTICAL;
        $start = min($x1, $x2);
        $end   = max($x1, $x2);
    }

    if ($x1 !== $x2 && $y1 !== $y2) {
        continue;
    }

    for ($pos = $start; $pos <= $end; ++$pos) {
        if ($direction === HORIZONTAL) {
            if (DEBUG) {
                printf('Crossing %d,%d' . PHP_EOL, $x1, $pos);
            }

            ++$map[$x1][$pos];
        }

        if ($direction === VERTICAL) {
            if (DEBUG) {
                printf('Crossing %d,%d' . PHP_EOL, $pos, $y1);
            }

            ++$map[$pos][$y1];
        }
    }
}

$overlappingLines = countOverlappingLines($map);

printf('Number of overlapping lines: %d' . PHP_EOL, $overlappingLines);

$input->rewind();

// Prefill map with 0
$map = array_fill(0, MAP_SIZE, null);
for ($row = 0; $row < MAP_SIZE; ++$row) {
    $map[$row] = array_fill(0, MAP_SIZE, 0);
}

while (! $input->eof() && $line = trim($input->fgets())) {
    [
        $y1,
        $x1,
        $y2,
        $x2,
    ] = preg_split('/(,| -> )/', $line);

    $x1 = (int) $x1;
    $y1 = (int) $y1;
    $x2 = (int) $x2;
    $y2 = (int) $y2;

    if (DEBUG) {
        printf('%d,%d -> %d,%d' . PHP_EOL, $x1, $y1, $x2, $y2);
    }

    $direction = DIAGONAL;

    if ($x1 === $x2) {
        $direction = HORIZONTAL;
        $start = min($y1, $y2);
        $end   = max($y1, $y2);
    }

    if ($y1 === $y2) {
        $direction = VERTICAL;
        $start = min($x1, $x2);
        $end   = max($x1, $x2);
    }

    if ($direction === DIAGONAL) {
        $slope  = ($y2 - $y1) / ($x2 - $x1);
        $offset = -($slope * $x1 - $y1);

        // Swap coordinates for line calculation
        // so we always go in the same direction
        if ($x1 > $x2) {
            // x1,y1 -> x3,y3
            $x3 = $x1;
            $y3 = $y1;

            // x2,y2 -> x1,y1
            $x1 = $x2;
            $y1 = $y2;

            // x3,y3 -> x2,y2
            $x2 = $x3;
            $y2 = $y3;
        }

        while ($x1 !== $x2) {
            if (DEBUG) {
                printf('Crossing %d,%d' . PHP_EOL, $x1, $y1);
            }
            ++$map[$x1][$y1];

            ++$x1;
            $y1 = $slope * $x1 + $offset;
        }

        if (DEBUG) {
            printf('Crossing %d,%d' . PHP_EOL, $x2, $y2);
        }
        ++$map[$x2][$y2];

        continue;
    }

    for ($pos = $start; $pos <= $end; ++$pos) {
        if ($direction === HORIZONTAL) {
            if (DEBUG) {
                printf('Crossing %d,%d' . PHP_EOL, $x1, $pos);
            }

            ++$map[$x1][$pos];
        }

        if ($direction === VERTICAL) {
            if (DEBUG) {
                printf('Crossing %d,%d' . PHP_EOL, $pos, $y1);
            }

            ++$map[$pos][$y1];
        }
    }
}

$overlappingLines = countOverlappingLines($map);

printf('Number of overlapping lines with diagonals: %d' . PHP_EOL, $overlappingLines);
