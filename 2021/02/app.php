<?php

$input = new SplFileObject('input.txt');

$x = 0;
$y = 0;
while (! $input->eof() && $line = $input->fgets()) {
    [$direction, $units] = explode(' ', trim($line));

    switch ($direction) {
        case 'forward':
            $x += $units;
            break;

        case 'up':
            $y -= $units;
            break;

        case 'down':
            $y += $units;
            break;
    }
}

echo 'Horizontal position: ' . $x . PHP_EOL;
echo 'Depth:               ' . $y . PHP_EOL;
echo 'Multiplicative sum:  ' . $x * $y . PHP_EOL;

$input->rewind();

$x   = 0;
$y   = 0;
$aim = 0;
while (! $input->eof() && $line = $input->fgets()) {
    [$direction, $units] = explode(' ', trim($line));

    switch ($direction) {
        case 'forward':
            $x += $units;
            $y += $units * $aim;
            break;

        case 'up':
            $aim -= $units;
            break;

        case 'down':
            $aim += $units;
            break;
    }
}

echo 'Horizontal position: ' . $x . PHP_EOL;
echo 'Depth:               ' . $y . PHP_EOL;
echo 'Multiplicative sum:  ' . $x * $y . PHP_EOL;
