<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('NUMBERS', [
    'one'   => '1',
    'two'   => '2',
    'three' => '3',
    'four'  => '4',
    'five'  => '5',
    'six'   => '6',
    'seven' => '7',
    'eight' => '8',
    'nine'  => '9',
]);

$values = [];

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    preg_match_all('/\d/', $line, $matches);

    $values[] = (int) (reset($matches[0]) . end($matches[0]));
}

printf('Sum of all calibration values: %d' . PHP_EOL, array_sum($values));

$values = [];

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    foreach (NUMBERS as $string => $integer) {
        $line = str_replace(search: $string, replace: "{$string}{$integer}{$string}", subject: $line);
    }

    preg_match_all('/\d/', $line, $matches);

    $values[] = (int) (reset($matches[0]) . end($matches[0]));
}

printf('Sum of all calibration values: %d' . PHP_EOL, array_sum($values));
