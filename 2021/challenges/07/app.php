<?php

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

$input = new SplFileObject(INPUT_FILE);

$crabs = explode(',', trim($input->fgets()));

$distances = array_count_values($crabs);
$closest   = min($crabs);
$furthest  = max($crabs);

// Part 1
$totals = [];
for ($waypoint = $closest; $waypoint <= $furthest; ++$waypoint) {
    $totals[$waypoint] = 0;

    foreach ($distances as $distance => $count) {
        if ($waypoint < $distance) {
            $totals[$waypoint] += ($distance - $waypoint) * $count;
        }

        if ($distance < $waypoint) {
            $totals[$waypoint] += ($waypoint - $distance) * $count;
        }
    }
}

$leastGases = array_keys($totals, min($totals));

if (count($leastGases) > 1) {
    echo 'More than one answer!' . PHP_EOL;
}

foreach ($leastGases as $leastGas) {
    printf('Way point %d costs %d' . PHP_EOL, $leastGas, $totals[$leastGas]);
}

// Part 2
$totals = [];
for ($waypoint = $closest; $waypoint <= $furthest; ++$waypoint) {
    $totals[$waypoint] = 0;

    foreach ($distances as $distance => $count) {
        if ($waypoint < $distance) {
            $cost = array_sum(range(1, ($distance - $waypoint)));
        }

        if ($distance < $waypoint) {
            $cost = array_sum(range(1, ($waypoint - $distance)));
        }

        $totals[$waypoint] += $cost * $count;
    }
}

$leastGases = array_keys($totals, min($totals));

if (count($leastGases) > 1) {
    echo 'More than one answer!' . PHP_EOL;
}

foreach ($leastGases as $leastGas) {
    printf('Way point %d costs %d' . PHP_EOL, $leastGas, $totals[$leastGas]);
}

