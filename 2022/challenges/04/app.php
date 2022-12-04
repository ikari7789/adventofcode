<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

$input = new SplFileObject(INPUT_FILE);
$sets = [];
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);
    $elves = explode(',', $line);

    foreach ($elves as &$elf) {
        $set = explode('-', $elf);
        $elf = range($set[0], $set[1]);
    }
    unset($elf);

    array_push($sets, $elves);
}

$hasOverlap = 0;
foreach ($sets as $elves) {
    $intersection = array_values(array_intersect(...$elves));

    if ($elves[0] === $intersection || $elves[1] === $intersection) {
        $hasOverlap++;
    }
}

echo "Groups with complete overlap: {$hasOverlap}" . PHP_EOL;

$hasOverlap = 0;
foreach ($sets as $elves) {
    $intersection = array_values(array_intersect(...$elves));

    if (! empty($intersection)) {
        $hasOverlap++;
    }
}

echo "Groups with overlap: {$hasOverlap}" . PHP_EOL;
