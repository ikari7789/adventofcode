<?php

require __DIR__ . '/../../vendor/autoload.php';

use Ikari7789\Adventofcode\Year2021\Day18\SnailfishNumber;

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/example_input.txt');

function ingestInput(string $path): array
{
    $input = new SplFileObject(INPUT_FILE);

    $numbers = [];
    while (! $input->eof() && $line = trim($input->fgets())) {
        if (! preg_match('/[0-9,\[\]]+/', $line)) {
            continue;
        }

        eval('$numbers[] = ' . $line . ';');
    }

    return $numbers;
}

$numbers = ingestInput(INPUT_FILE);

foreach ($numbers as $number) {
    $snailfishNumber = new SnailfishNumber($number);
    $snailfishNumber->reduce();
    // dd($snailfishNumber);
}
