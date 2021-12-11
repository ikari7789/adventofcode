<?php

$input = new SplFileObject(__DIR__ . '/input.txt');

$deeperMeasurements = 0;
$previousDepth = null;
while (! $input->eof() && $line = $input->fgets()) {
    $depth = (int) trim($line);
    
    if ($previousDepth !== null && $previousDepth < $depth) {
        ++$deeperMeasurements;
    }

    $previousDepth = $depth;
}

echo 'Measurements larger than the previous measurement: ' . $deeperMeasurements . PHP_EOL;

$input->rewind();

$deeperMeasurements = 0;
$previousDepths = [];
$sizeOfRollingSum = 3;
while (! $input->eof() && $line = $input->fgets()) {
    $depth = (int) trim($line);

    if (count($previousDepths) === $sizeOfRollingSum + 1) {
        $rollingSum1 = array_sum(array_slice($previousDepths, 0, $sizeOfRollingSum));

        $previousDepths = array_slice($previousDepths, 1, $sizeOfRollingSum);

        $rollingSum2 = array_sum($previousDepths);

        if ($rollingSum2 > $rollingSum1) {
            ++$deeperMeasurements;
        }
    }

    $previousDepths[] = $depth;
}

$rollingSum1 = array_sum(array_slice($previousDepths, 0, $sizeOfRollingSum));

$previousDepths = array_slice($previousDepths, 1, $sizeOfRollingSum);

$rollingSum2 = array_sum($previousDepths);

if ($rollingSum2 > $rollingSum1) {
    ++$deeperMeasurements;
}

echo 'Measurements larger than the previous measurement: ' . $deeperMeasurements . PHP_EOL;
