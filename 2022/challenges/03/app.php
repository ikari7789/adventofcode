<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Ikari7789\Adventofcode\Year2022\Day3\Rucksack;

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('COMPARTMENT_COUNT', 2);
define('GROUP_SIZE', 3);

$input = new SplFileObject(INPUT_FILE);
$rucksacks = [];
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);
    array_push($rucksacks, new Rucksack($line, COMPARTMENT_COUNT));
}

$priorities = [];
foreach ($rucksacks as $rucksack) {
    array_push($priorities, $rucksack->compartmentIntersection());
}

echo 'Sum of priorities: ' . array_sum($priorities) . PHP_EOL;

$groups = array_chunk($rucksacks, GROUP_SIZE);

$groupPriorities = [];
foreach ($groups as $group) {
    $items = [];
    foreach ($group as $rucksack) {
        array_push($items, str_split($rucksack->items));
    }
    array_push($groupPriorities, Rucksack::PRIORITIES[array_values(array_unique(array_intersect(...$items)))[0]]);
}

echo 'Sum of group priorities: ' . array_sum($groupPriorities) . PHP_EOL;
