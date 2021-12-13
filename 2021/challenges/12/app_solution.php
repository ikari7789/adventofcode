<?php

/**
 * Courtesy: https://github.com/duemti/adventofcode/blob/78e24c1e503cc4721edea08cc144df774664ccb8/2021/day_12_Passage_Pathing/solve.php
 */

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

$caves = [];

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = trim($input->fgets())) {
    [
        $a,
        $b,
    ] = explode('-', $line);

    $caves[$a][] = $b;
    $caves[$b][] = $a;
}

function partOne(array $caves, bool $allowTwice = false): int
{
    $paths = [
        ['start']
    ];
    $countPaths = 0;

    while (! empty($paths)) {
        $current = array_pop($paths);

        foreach ($caves[$current[0]] as $nextCavern) {
            if (ctype_lower($nextCavern) && in_array($nextCavern, $current)) {
                if (! $allowTwice || in_array('double', $current) || $nextCavern === 'start') {
                    continue;
                }

                $paths[] = array_merge([$nextCavern, 'double'], $current);
            } elseif ($nextCavern === 'end') {
                ++$countPaths;
            } else {
                $paths[] = array_merge([$nextCavern], $current);
            }
        }
    }

    return $countPaths;
}

function partTwo(array $caves): int
{
    return partOne($caves, true);
}

// PART 1
echo "Part 1: There are \e[32m" . partOne($caves) . "\e[0m paths that visit at most once a small cave." . PHP_EOL;

// PART 2
echo "Part 2: But allowing for only one cavern to be visited twice, then there are \e[32m" . partTwo($caves) . "\e[0m" . PHP_EOL;
