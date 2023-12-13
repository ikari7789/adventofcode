<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

function findHorizontalReflection(array $pattern): array
{
    $reflections = [];

    // Check for horizontal mirroring
    for ($y = 0; $y < count($pattern) - 1; ++$y) {
        if ($pattern[$y] === $pattern[$y + 1]) {
            // Bubble out for each row until a non-match is found
            for ($x = 1; $x <= $y; ++$x) {
                // If any row doesn't match, then continue checking for a new horiontal match
                if (isset($pattern[$y - $x], $pattern[$y + 1 + $x])) {
                    if ($pattern[$y - $x] !== $pattern[$y + 1 + $x]) {
                        continue 2;
                    }
                }
            }
            unset($x);

            $reflections[] = $y + 1;
        }
    }
    unset($y);

    return $reflections;
}

function findVerticalReflection(array $pattern): array
{
    return findHorizontalReflection(rotatePatternRight($pattern));
}

function rotatePatternRight(array $pattern): array
{
    $flippedPattern = [];

    $patternCount = count($pattern);
    for ($x = 0; $x < strlen($pattern[0]); ++$x) {
        for ($y = 0; $y < $patternCount; ++$y) {
            $flippedPattern[$x][count($pattern) - $y] = $pattern[$y][$x];
        }
        $flippedPattern[$x] = array_reverse($flippedPattern[$x]);
    }

    return array_map(fn($line) => implode('', $line), $flippedPattern);
}

function findReflections(array $pattern): array
{
    // Check for horizontal mirroring
    $horizontalReflection = findHorizontalReflection($pattern);

    // Check for vertical mirroring
    $verticalReflection = findVerticalReflection($pattern);

    $result = [
        'horizontal' => $horizontalReflection,
        'vertical'   => $verticalReflection,
    ];

    return $result;
}

function summarizeReflections(array $reflections): int
{
    $sum = 0;

    foreach ($reflections as $reflection) {
        $columnsBefore = 0;
        if (isset($reflection['vertical'][0])) {
            $columnsBefore = $reflection['vertical'][0];
        }

        $rowsAbove = 0;
        if (isset($reflection['horizontal'][0])) {
            $rowsAbove = $reflection['horizontal'][0];
        }

        $sum += $columnsBefore + ($rowsAbove * 100);
    }

    return $sum;
}

$pattern = [];
$patterns = [];

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    if (empty($line)) {
        $patterns[] = $pattern;
        $pattern = [];
        continue;
    }

    $pattern[] = $line;
}
$patterns[] = $pattern;

$reflections = [];
$smudgeReflections = [];
foreach ($patterns as $pattern) {
    $patternReflection = [];

    $patternReflection['reflection'] = findReflections($pattern);

    // Fix smudge
    $smudgePatterns = [];
    for ($y = 0; $y < count($pattern); ++$y) {
        for ($x = 0; $x < strlen($pattern[$y]); ++$x) {
            $smudgePattern = $pattern;
            $smudgePattern[$y] = substr_replace(
                $smudgePattern[$y],
                substr($smudgePattern[$y], $x, 1) === '#' ? '.' : '#',
                $x,
                1
            );

            $patternReflection['smudge'] = findReflections($smudgePattern);

            if (
                $patternReflection['smudge'] !== $patternReflection['reflection']
                && (
                    count($patternReflection['smudge']['horizontal']) !== 0
                    || count($patternReflection['smudge']['vertical']) !== 0
                )
            ) {
                $patternReflection['smudge']['horizontal'] = array_diff($patternReflection['smudge']['horizontal'], $patternReflection['reflection']['horizontal']);
                $patternReflection['smudge']['vertical'] = array_diff($patternReflection['smudge']['vertical'], $patternReflection['reflection']['vertical']);
                $patternReflection['smudge']['horizontal'] ??= [];
                $patternReflection['smudge']['vertical'] ??= [];

                $smudgeReflections[] = $patternReflection['smudge'];

                break 2;
            }
        }
    }

    $reflections[] = $patternReflection['reflection'];
}

printf('Summary of reflections: %d' . PHP_EOL, summarizeReflections($reflections));
printf('Summary of reflections after cleaning smudge: %d' . PHP_EOL, summarizeReflections($smudgeReflections));
