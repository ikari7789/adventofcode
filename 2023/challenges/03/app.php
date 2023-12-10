<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

function printSchematic(array $schematic): void
{
    foreach ($schematic as $row => $columns) {
        foreach ($columns as $column) {
            echo $column;
        }
        echo PHP_EOL;
    }
    echo PHP_EOL;
}

function readPartNumber(array $schematic, int $x, int $y): ?array
{
    if (!is_numeric($schematic[$y][$x])) {
        return null;
    }

    // Increase startPos by one at the end because we subtract one too far
    $startPos = $x;
    while ($startPos > -1 && is_numeric($schematic[$y][$startPos])) {
        --$startPos;
    }
    ++$startPos;

    $pos    = $startPos;
    $number = '';
    while ($pos < count($schematic[$y]) && is_numeric($schematic[$y][$pos])) {
        $number .= $schematic[$y][$pos];
        ++$pos;
    }

    return [
        'number' => (int) $number,
        'position' => [
            'x' => $startPos,
            'y' => $y,
        ],
    ];
}

function discoverPartNumbers(array $schematic, int $x, int $y): array
{
    $partNumber = [];

    if ($y > 0) {
        if ($x > 0) {
            // top left
            $partNumbers[] = readPartNumber($schematic, $x - 1, $y - 1);
        }

        // top
        $partNumbers[] = readPartNumber($schematic, $x, $y - 1);

        if ($x < count($schematic[$y])) {
            // top right
            $partNumbers[] = readPartNumber($schematic, $x + 1, $y - 1);
        }
    }

    if ($x > 0) {
        // left
        $partNumbers[] = readPartNumber($schematic, $x - 1, $y);
    }

    if ($x < count($schematic[$y])) {
        // right
        $partNumbers[] = readPartNumber($schematic, $x + 1, $y);
    }

    if ($y < count($schematic)) {
        if ($x > 0) {
            // bottom left
            $partNumbers[] = readPartNumber($schematic, $x - 1, $y + 1);
        }

        // bottom
        $partNumbers[] = readPartNumber($schematic, $x, $y + 1);

        if ($x < count($schematic[$y])) {
            // bottom right
            $partNumbers[] = readPartNumber($schematic, $x + 1, $y + 1);
        }
    }

    // Remove null and duplicate entries
    return array_values(
        array_unique(
            array_filter($partNumbers),
            SORT_REGULAR
        )
    );
}

function discoverParts(array $schematic): array
{
    $parts = [];

    for ($y = 0; $y < count($schematic); ++$y) {
        for ($x = 0; $x < count($schematic[$y]); ++$x) {
            if (!preg_match('/[^\d\.]/', $schematic[$y][$x])) {
                continue;
            }

            $parts[] = [
                'symbol'   => $schematic[$y][$x],
                'position' => [
                    'x' => $x,
                    'y' => $y,
                ],
                'numbers' => discoverPartNumbers($schematic, $x, $y),
            ];
        }
    }

    return $parts;
}

function sumPartNumbers(array $parts): int
{
    $sum = 0;

    foreach ($parts as $part) {
        foreach ($part['numbers'] as $number) {
            $sum += $number['number'];
        }
    }

    return $sum;
}

function printParts(array $parts): void
{
    foreach ($parts as $part) {
        printf('Part [%s]: (%03d, %03d)' . PHP_EOL, $part['symbol'], $part['position']['x'], $part['position']['y']);

        foreach ($part['numbers'] as $number) {
            printf("\t%d" . PHP_EOL, $number['number']);
        }
    }
}

function sumGearRatios(array $parts): int
{
    $sum = 0;

    foreach ($parts as $part) {
        if ($part['symbol'] !== '*') {
            continue;
        }

        if (count($part['numbers']) !== 2) {
            continue;
        }

        $sum += $part['numbers'][0]['number'] * $part['numbers'][1]['number'];
    }

    return $sum;
}

$schematic = [];

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    $schematic[] = mb_str_split($line);
}

// printSchematic($schematic);

$parts = discoverParts($schematic);

// printParts($parts);

printf('Sum of all part numbers: %d' . PHP_EOL, sumPartNumbers($parts));
printf('Sum of all gear ratios:  %d' . PHP_EOL, sumGearRatios($parts));
