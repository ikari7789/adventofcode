<?php

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', false);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('NUMBER_OF_STEPS_PART_1', 10);
define('NUMBER_OF_STEPS_PART_2', 40);

$input = new SplFileObject(INPUT_FILE);

$polymerTemplate = '';
$pairInsertionRules = [];

$lineCount = 0;
while (! $input->eof() && $line = $input->fgets()) {
    ++$lineCount;
    $line = trim($line);

    if (preg_match('/^[A-Z]+$/', $line)) {
        $polymerTemplate = str_split($line);

        continue;
    }

    if (preg_match('/^[A-Z]{2} -> [A-Z]{1}$/', $line)) {
        [
            $pair,
            $insertion,
        ] = explode(' -> ', $line);
        $pairInsertionRules[$pair] = $insertion;

        continue;
    }
}

assert($lineCount === count($pairInsertionRules) + 2, 'Number of insertion rules should equal line count plus template');

function insertPolymer(array &$polymer, array $insertionRules): void
{
    for ($index = 0; $index < count($polymer) - 1; ++$index) {
        $slice = $polymer[$index] . $polymer[$index + 1];

        foreach ($insertionRules as $pair => $insertion) {
            if ($slice === $pair) {
                array_splice($polymer, $index + 1, 0, [$insertion]);
                ++$index;

                break;
            }
        }
    }
}

function convert(int $size): string
{
    $units = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $units[$i];
}

if (DEBUG) {
    printf('Template:     %s' . PHP_EOL, implode('', $polymerTemplate));
}

$polymer = $polymerTemplate;
for ($step = 1; $step <= NUMBER_OF_STEPS_PART_2; ++$step) {
    insertPolymer($polymer, $pairInsertionRules);

    printf('After step %d: (memory: %s)', $step, convert(memory_get_usage(true)));

    if (DEBUG) {
        printf(' %s', implode('', $polymer));
    }

    echo PHP_EOL;

    if ($step === NUMBER_OF_STEPS_PART_1 || $step === NUMBER_OF_STEPS_PART_2) {
        $polymerCounts = array_count_values($polymer);

        $leastCommon = min($polymerCounts);
        $mostCommon  = max($polymerCounts);

        printf('Step %d - Most common minus least common: %d' . PHP_EOL, $step, $mostCommon - $leastCommon);
    }
}