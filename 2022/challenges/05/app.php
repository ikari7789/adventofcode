<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('CRATES_FILE', __DIR__ . '/crates.txt');
define('MOVEMENTS_FILE', __DIR__ . '/movements.txt');

define('CRATE_COLUMN_WIDTH', 4);

function generateStacks(string $inputFile): array
{
    $stacks = [];

    $input = new SplFileObject($inputFile);
    while (! $input->eof() && $line = $input->fgets()) {
        $numberOfCrates = strlen($line) / CRATE_COLUMN_WIDTH;
    
        $position = 0;
        while ($position < strlen($line)) {
            $crate = trim(substr($line, $position, CRATE_COLUMN_WIDTH));
            $index = $position / CRATE_COLUMN_WIDTH;
    
            if (preg_match('/\[[A-Z]\]$/', $crate)) {
                $crate = substr($crate, 1, 1);
    
                if (! isset($stacks[$index])) {
                    $stacks[$index] = [];
                }
    
                array_unshift($stacks[$index], $crate);
            }
    
            $position += CRATE_COLUMN_WIDTH;
        }
    }
    ksort($stacks);

    return $stacks;
}

function generateMovements(string $inputFile): array
{
    $movements = [];

    $input = new SplFileObject($inputFile);
    while (! $input->eof() && $line = $input->fgets()) {
        $line = trim($line);

        preg_match('/^move (?P<count>\d+) from (?P<from>\d+) to (?P<to>\d+)$/', $line, $matches);

        $movements[] = [
            'count' => (int) $matches['count'],
            'from'  => (int) $matches['from'] - 1,
            'to'    => (int) $matches['to'] - 1,
        ];
    }

    return $movements;
}

function moveCratesOneByOne(array $stacks, array $movements): array
{
    foreach ($movements as $movement) {
        for ($count = 0; $count < $movement['count']; ++$count) {

            array_push($stacks[$movement['to']], array_pop($stacks[$movement['from']]));
        }
    }

    return $stacks;
}

function moveCratesAsGroup(array $stacks, array $movements): array
{
    foreach ($movements as $movement) {
        array_push(
            $stacks[$movement['to']],
            ...array_splice(
                $stacks[$movement['from']],
                count($stacks[$movement['from']]) - $movement['count'],
                $movement['count']
            )
        );
    }

    return $stacks;
}

function topCrates(array $stacks): string
{
    $topCrates = '';

    foreach ($stacks as $stack) {
        $topCrates .= array_pop($stack);
    }

    return $topCrates;
}

$stacks    = generateStacks(CRATES_FILE);
$movements = generateMovements(MOVEMENTS_FILE);

$rearrangedStacks = moveCratesOneByOne($stacks, $movements);

dump(topCrates($rearrangedStacks));

$rearrangedStacks = moveCratesAsGroup($stacks, $movements);

dump(topCrates($rearrangedStacks));
