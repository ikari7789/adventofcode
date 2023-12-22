<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input_2.txt');
define('MAX_STEPS', 65);

// 26501365 / 65 = 407713r20 -> 3752 steps
// 26501365 / 130 = 203856r85 -> 14865 steps

// /**
//  * Return map repeated into 9x9 with original in center.
//  */
// function explodeMap(array $map): array
// {
//     $newMap = [];

//     for ($multiplier = 0; $multiplier < 3; ++$multiplier) {
//         $newMap = array_merge($newMap ?? [], $newMap ?? []);
//         for ($row = 0; $row < count($map); ++$row) {
//             $newMap[$row] = array_merge($newMap[$row] ?? [], array_map(fn ($point) => $point === 'O' ? '.' : $point, $newMap[$row]));
//         }
//     }

//     return $newMap;
// }

function takeStep(array $map): array
{
    $newMap = $map;

    for ($y = 0; $y < count($map); ++$y) {
        for ($x = 0; $x < count($map[$y]); ++$x) {
            if ($map[$y][$x] === 'O') {
                $newMap[$y][$x] = '.';
                if (isset($map[$y - 1][$x]) && $map[$y - 1][$x] !== '#') {
                    $newMap[$y - 1][$x] = 'O';
                }
                if (isset($map[$y + 1][$x]) && $map[$y + 1][$x] !== '#') {
                    $newMap[$y + 1][$x] = 'O';
                }
                if (isset($map[$y][$x - 1]) && $map[$y][$x - 1] !== '#') {
                    $newMap[$y][$x - 1] = 'O';
                }
                if (isset($map[$y][$x + 1]) && $map[$y][$x + 1] !== '#') {
                    $newMap[$y][$x + 1] = 'O';
                }
            }
        }
    }

    return $newMap;
}

function countGardenPlots(array $map): int
{
    $gardenPlots = 0;

    foreach ($map as $row) {
        foreach ($row as $column) {
            if ($column === 'O') {
                ++$gardenPlots;
            }
        }
    }

    return $gardenPlots;
}

function printMap(array $map): void
{
    foreach ($map as $row) {
        foreach ($row as $column) {
            echo $column;
        }
        echo PHP_EOL;
    }
    echo PHP_EOL;
}

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $map[] = str_split(str_replace('S', 'O', trim($line)));
}

printMap($map);

// $map = explodeMap($map);
// printMap($map);

for ($step = 0; $step < MAX_STEPS; ++$step) {
    $map = takeStep($map);
    // printMap($map);
    // printf('Garden plots the Elf could reach in %d steps: %d' . PHP_EOL, MAX_STEPS, countGardenPlots($map));
}

printMap($map);

printf('Garden plots the Elf could reach in %d steps: %d' . PHP_EOL, MAX_STEPS, countGardenPlots($map));
