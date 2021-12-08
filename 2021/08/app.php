<?php

define('DEBUG', false);
define('INPUT_FILE', 'input.txt');

$input = new SplFileObject(INPUT_FILE);

function sortByLength(array $array): array
{
    usort($array, function ($a, $b) {
        return strlen($a) - strlen($b);
    });

    return $array;
}

function arrangeAlphabetically(array $array): array {
    return array_map(function ($value) {
        $parts = str_split($value);
        sort($parts);
        return implode($parts);
    }, $array);
}

function processSignals(array $signalPatterns): array
{
    $signalMapping = [
        0 => [
            'original' => 'abcefg',
            'mapped'   => null,
        ],
        1 => [
            'original' => 'cf',
            'mapped'   => null,
        ],
        2 => [
            'original' => 'acdeg',
            'mapped'   => null,
        ],
        3 => [
            'original' => 'acdfg',
            'mapped'   => null,
        ],
        4 => [
            'original' => 'bcdf',
            'mapped'   => null,
        ],
        5 => [
            'original' => 'abdfg',
            'mapped'   => null,
        ],
        6 => [
            'original' => 'abdefg',
            'mapped'   => null,
        ],
        7 => [
            'original' => 'acf',
            'mapped'   => null,
        ],
        8 => [
            'original' => 'abcdefg',
            'mapped'   => null,
        ],
        9 => [
            'original' => 'abcdfg',
            'mapped'   => null,
        ],
    ];

    $signalPatterns = sortByLength($signalPatterns);
    $signalPatterns = arrangeAlphabetically($signalPatterns);

    // Base

    // 2 : cf      : 1 ==> [0]
    // 3 : acf     : 7 ==> [1]
    // 4 : bcdf    : 4 ==> [2]
    // 7 : abcdefg : 8 ==> [9]

    // 5 : acdeg   : 2 ==> [3, 4, 5]
    // 5 : acdfg   : 3 ==> [3, 4, 5]
    // 5 : abdfg   : 5 ==> [3, 4, 5]

    // 6 : abcefg  : 0 ==> [6, 7, 8]
    // 6 : abdefg  : 6 ==> [6, 7, 8]
    // 6 : abcdfg  : 9 ==> [6, 7, 8]

    // For mapping

    // 1, 4, 7, 8 are given
    // Using 7, we can determine 3
    // Using 7 + 4, we can determine 5
    // Process of elimination, we can determine 2
    // Using 5, we can determine 0
    // Using 4, we can determine 9
    // Process of elimination, we can determine 6

    // Map 1
    $signalMapping[1]['mapped'] = $signalPatterns[0];

    // Map 4
    $signalMapping[4]['mapped'] = $signalPatterns[2];

    // Map 7
    $signalMapping[7]['mapped'] = $signalPatterns[1];

    // Map 8
    $signalMapping[8]['mapped'] = $signalPatterns[9];

    // Map 2, 3, and 5
    $indexesToMap = [3, 4, 5];

    // Map 3
    foreach ($indexesToMap as $key => $index) {
        $signalPattern = $signalPatterns[$index];

        foreach (str_split($signalMapping[7]['mapped']) as $char) {
            $signalPattern = str_replace($char, '', $signalPattern);
        }

        if (strlen($signalPattern) === 2) {
            $signalMapping[3]['mapped'] = $signalPatterns[$index];
            unset($indexesToMap[$key]);

            break;
        }
    }

    // Map 5
    foreach ($indexesToMap as $key => $index) {
        $signalPattern = $signalPatterns[$index];

        foreach (str_split($signalMapping[7]['mapped']) as $char) {
            $signalPattern = str_replace($char, '', $signalPattern);
        }

        foreach (str_split($signalMapping[4]['mapped']) as $char) {
            $signalPattern = str_replace($char, '', $signalPattern);
        }

        if (strlen($signalPattern) === 1) {
            $signalMapping[5]['mapped'] = $signalPatterns[$index];
            unset($indexesToMap[$key]);

            break;
        }
    }

    // Map 2
    $signalMapping[2]['mapped'] = $signalPatterns[array_values($indexesToMap)[0]];

    // Map 0, 6, and 9
    $indexesToMap = [6, 7, 8];

    // Map 0
    foreach ($indexesToMap as $key => $index) {
        $signalPattern = $signalPatterns[$index];

        foreach (str_split($signalMapping[5]['mapped']) as $char) {
            $signalPattern = str_replace($char, '', $signalPattern);
        }

        if (strlen($signalPattern) === 2) {
            $signalMapping[0]['mapped'] = $signalPatterns[$index];
            unset($indexesToMap[$key]);

            break;
        }
    }

    // Map 9
    foreach ($indexesToMap as $key => $index) {
        $signalPattern = $signalPatterns[$index];

        foreach (str_split($signalMapping[4]['mapped']) as $char) {
            $signalPattern = str_replace($char, '', $signalPattern);
        }

        if (strlen($signalPattern) === 2) {
            $signalMapping[9]['mapped'] = $signalPatterns[$index];
            unset($indexesToMap[$key]);

            break;
        }
    }

    // Map 6
    $signalMapping[6]['mapped'] = $signalPatterns[array_values($indexesToMap)[0]];

    return array_flip(array_map(function ($value) {
        return $value['mapped'];
    }, $signalMapping));
}

function decodeOutput(array $outputValue, array $signalMapping): int
{
    $outputValue = arrangeAlphabetically($outputValue);

    $decodedOutput = '';
    foreach ($outputValue as $value) {
        $decodedOutput .= $signalMapping[$value];
    }

    return (int) $decodedOutput;
}

$totalUniqueDigits = 0;
$sumOfDecodedOutputs = 0;
while (! $input->eof() && $line = trim($input->fgets())) {
    [
        $signalPatterns,
        $outputValue,
    ] = explode(' | ', $line);

    $signalPatterns = explode(' ', $signalPatterns);
    $outputValue    = explode(' ', $outputValue);

    $uniqueDigits = array_reduce($outputValue, function ($uniqueDigits, $value) {
        $digitCount = strlen($value);

        if (in_array($digitCount, [2, 3, 4, 7])) {
            ++$uniqueDigits;
        }

        return $uniqueDigits;
    }, 0);

    $totalUniqueDigits += $uniqueDigits;

    $signalMapping = processSignals($signalPatterns);
    $decodedOutput = decodeOutput($outputValue, $signalMapping);

    if (DEBUG) {
        printf('Decoded output: %d' . PHP_EOL, $decodedOutput);
    }

    $sumOfDecodedOutputs += $decodedOutput;
}

printf('Number of 1s, 4s, 7s, and 8s: %d' . PHP_EOL, $totalUniqueDigits);
printf('Sum of decoded outputs: %d' . PHP_EOL, $sumOfDecodedOutputs);
