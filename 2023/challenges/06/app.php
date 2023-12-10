<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

function calculateDistance(int $holdTime, int $raceTime): int
{
    $travelTime = $raceTime - $holdTime;
    $speed      = $holdTime;

    return $travelTime * $speed;
}

function calculateMinHoldTime(array $race): ?int
{
    for ($holdTime = 0; $holdTime < $race['time']; ++$holdTime) {
        $distance = calculateDistance($holdTime, $race['time']);

        if ($distance > $race['distance']) {
            return $holdTime;
        }
    }

    return null;
}

function calculateMaxHoldTime(array $race): ?int
{
    for ($holdTime = $race['time']; $holdTime > 0; --$holdTime) {
        $distance = calculateDistance($holdTime, $race['time']);

        if ($distance > $race['distance']) {
            return $holdTime;
        }
    }

    return null;
}

$times     = [];
$distances = [];

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    if (preg_match('/^Time/', $line)) {
        $times = array_values(array_map(fn($item) => (int) $item, array_filter(explode(' ', explode(':', $line)[1]))));
    } elseif (preg_match('/^Distance/', $line)) {
        $distances = array_values(array_map(fn($item) => (int) $item, array_filter(explode(' ', explode(':', $line)[1]))));
    }
}

$races = [];
for ($index = 0; $index < count($times); ++$index) {
    $races[] = [
        'time'     => $times[$index],
        'distance' => $distances[$index],
    ];
}

$product = 1;
foreach ($races as &$race) {
    $minHoldTime = calculateMinHoldTime($race);
    $maxHoldTime = calculateMaxHoldTime($race);

    // Add 1 since it should be inclusive of the min/max
    $race['potential_wins'] = $maxHoldTime - $minHoldTime + 1;

    // printf('Time to beat: %d, Min Hold Time: %d, Max Hold Time: %d, Potential Wins: %d' . PHP_EOL, $race['time'], $minHoldTime, $maxHoldTime, $race['potential_wins']);

    $product *= $race['potential_wins'];
}

printf('Product of all possible wins: %d' . PHP_EOL, $product);

$time     = 0;
$distance = 0;

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    if (preg_match('/^Time/', $line)) {
        $time = (int) implode('', array_filter(explode(' ', explode(':', $line)[1])));
    } elseif (preg_match('/^Distance/', $line)) {
        $distance = (int) implode('', array_filter(explode(' ', explode(':', $line)[1])));
    }
}

$race = [
    'time'     => $time,
    'distance' => $distance,
];

$minHoldTime = calculateMinHoldTime($race);
$maxHoldTime = calculateMaxHoldTime($race);

// Add 1 since it should be inclusive of the min/max
$race['potential_wins'] = $maxHoldTime - $minHoldTime + 1;

// printf('Time to beat: %d, Min Hold Time: %d, Max Hold Time: %d, Potential Wins: %d' . PHP_EOL, $race['time'], $minHoldTime, $maxHoldTime, $race['potential_wins']);

printf('Total possible wins: %d' . PHP_EOL, $race['potential_wins']);
