<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/example_input.txt');

function buildStack(array $bricks): array
{
    $stack = [];

    $maxX = 0;
    $maxY = 0;
    $maxZ = 0;

    foreach ($bricks as $brick) {
        [
            $x1,
            $y1,
            $z1,
        ] = $brick[0];

        [
            $x2,
            $y2,
            $z2,
        ] = $brick[1];

        for ($x = $x1; $x <= $x2; ++$x) {
            $maxX = max($maxX, $x);
            for ($y = $y1; $y <= $y2; ++$y) {
                $maxY = max($maxY, $y);
                for ($z = $z1; $z <= $z2; ++$z) {
                    $maxZ = max($maxZ, $z);
                    $stack[$x][$y][$z] = '#';
                }
            }
        }
    }

    for ($x = 0; $x <= $maxX; ++$x) {
        for ($y = 0; $y <= $maxY; ++$y) {
            for ($z = 0; $z <= $maxZ; ++$z) {
                if (isset($stack[$x][$y][$z])) {
                    continue;
                }

                $stack[$x][$y][$z] = '.';
                ksort($stack[$x][$y]);
            }
            ksort($stack[$x]);
        }
        ksort($stack);
    }

    return $stack;
}

$bricks = [];

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    [
        $brickStart,
        $brickEnd,
    ] = explode('~', $line);

    [
        $x,
        $y,
        $z,
    ] = array_map('intval', explode(',', $brickStart));

    $brickStart = [$x, $y, $z];

    [
        $x,
        $y,
        $z,
    ] = array_map('intval', explode(',', $brickEnd));

    $brickEnd = [$x, $y, $z];

    $bricks[] = [
        $brickStart,
        $brickEnd,
    ];
}

dump($bricks);

$stack = buildStack($bricks);

dump($stack);
