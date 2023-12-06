<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/example_input.txt');

define('START', ord('S'));
define('END', ord('E'));

function generateMap(string $inputFile): array
{
    $map = [];

    $input = new SplFileObject($inputFile);
    while (! $input->eof() && $line = $input->fgets()) {
        $line = trim($line);
        $map[] = array_map(function ($item) {
            return ord($item);
        }, str_split($line));
    }

    return $map;
}

function positionOf(array $map, int $value): array
{
    foreach ($map as $y => $row) {
        foreach ($row as $x => $column) {
            if ($map[$y][$x] === $value) {
                return [
                    'x' => $x,
                    'y' => $y,
                ];
            }
        }
    }
}

function walkMap(array $map, array $startPosition, array $endPosition): array
{
    $path = [];

    $up    = $map[$startPosition['y'] - 1][$startPosition['x']] ?? null;
    $down  = $map[$startPosition['y'] + 1][$startPosition['x']] ?? null;
    $left  = $map[$startPosition['y']][$startPosition['x'] - 1] ?? null;
    $right = $map[$startPosition['y']][$startPosition['x'] + 1] ?? null;

    dump($up, $down, $left, $right);

    return $path;
}

$map = generateMap(INPUT_FILE);

$startPosition = positionOf($map, START);
$endPosition   = positionOf($map, END);

dump($startPosition, $endPosition);

$path = walkMap($map, $startPosition, $endPosition);


