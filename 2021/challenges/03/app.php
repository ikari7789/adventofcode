<?php

$input = new SplFileObject(__DIR__ . '/input.txt');

$bits = [];
while (! $input->eof() && $line = trim($input->fgets())) {
    foreach (str_split($line) as $position => $bit) {
        if (! isset($bits[$position][$bit])) {
            $bits[$position][$bit] = 1;

            continue;
        }

        ++$bits[$position][$bit];
    }
}

$epsilon = '';
$gamma   = '';
foreach ($bits as $position) {
    $epsilonBit = '0';
    $gammaBit   = '1';

    if ($position[1] > $position[0]) {
        $epsilonBit = '1';
        $gammaBit   = '0';
    }

    $epsilon .= $epsilonBit;
    $gamma   .= $gammaBit;
}
$epsilonInt = bindec($epsilon);
$gammaInt = bindec($gamma);

echo 'Epsilon rate:      ' . $epsilonInt . PHP_EOL;
echo 'Gamma rate:        ' . $gammaInt . PHP_EOL;
echo 'Power consumption: ' . ($epsilonInt * $gammaInt) . PHP_EOL;

$input->rewind();

function determineHighLowBit(array $binaries, int $position = 0): array
{
    $counts = [0, 0];
    foreach ($binaries as $binary) {
        ++$counts[$binary[$position]];
    }

    return ($counts[1] >= $counts[0]) ? [1, 0] : [0, 1];
}

$binaries = [];
while (! $input->eof() && $line = trim($input->fgets())) {
    $binaries[] = $line;
}

$highBits = $binaries;
$lowBits   = $binaries;
for ($position = 0; $position < strlen($binaries[0]); ++$position) {
    if (count($highBits) > 1) {
        [
            $highBit,
            $lowBit,
        ] = determineHighLowBit($highBits, $position);
        
        $highBits = array_values(array_filter($highBits, function ($value) use ($position, $highBit) {
            return (int) $value[$position] === $highBit;
        }));
    }

    if (count($lowBits) > 1) {
        [
            $highBit,
            $lowBit,
        ] = determineHighLowBit($lowBits, $position);
        
        $lowBits = array_values(array_filter($lowBits, function ($value) use ($position, $lowBit) {
            return (int) $value[$position] === $lowBit;
        }));
    }
}

$oxygenGeneratorRating = bindec($highBits[0]);
$co2ScrubberRating     = bindec($lowBits[0]);

echo 'Oxygen generator rating: ' . $oxygenGeneratorRating . PHP_EOL;
echo 'CO2 scrubber rating:     ' . $co2ScrubberRating . PHP_EOL;
echo 'Life support rating:     ' . ($oxygenGeneratorRating * $co2ScrubberRating) . PHP_EOL;