<?php

error_reporting(E_ALL ^ E_DEPRECATED);

require __DIR__ . '/../../vendor/autoload.php';

use Ds\Stack;

define('DEBUG', false);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('TAGS', [
    '(' => ')',
    '[' => ']',
    '{' => '}',
    '<' => '>',
]);
define('ILLEGAL_SYNTAX_POINTS', [
    ')' => 3,
    ']' => 57,
    '}' => 1197,
    '>' => 25137,
]);
define('AUTOCOMPLETE_POINTS', [
    ')' => 1,
    ']' => 2,
    '}' => 3,
    '>' => 4,
]);

$input = new SplFileObject(INPUT_FILE);
$illegalCharacters = [];
$autocompleteCharacters = [];
while (! $input->eof() && $line = trim($input->fgets())) {
    $characters = str_split($line);

    if (DEBUG) {
        printf('Processing: %s' . PHP_EOL, $line);
    }

    $stack = new Stack();
    $illegalCharacter = null;
    foreach ($characters as $char) {
        if (in_array($char, array_keys(TAGS))) {
            if (DEBUG) {
                printf('%s%s' . PHP_EOL, str_repeat('  ', count($stack)), $char);
            }

            $stack->push($char);

            continue;
        }

        // Unmatched closing tag
        if ($char !== TAGS[$stack->peek()]) {
            printf(
                'Expected %s, but found %s instead.' . PHP_EOL,
                $stack->peek(),
                $char,
            );

            $illegalCharacter = $char;

            break;
        }

        $stack->pop();

        if (DEBUG) {
            printf('%s%s' . PHP_EOL, str_repeat('  ', count($stack)), $char);
        }
    }

    if ($illegalCharacter !== null) {
        $illegalCharacters[] = $illegalCharacter;

        continue;
    }

    $autocompleteLine = [];
    while (count($stack) > 0 && $char = $stack->pop()) {
        $autocompleteLine[] = TAGS[$char];
    }
    $autocompleteCharacters[] = [
        'line'         => $line,
        'autocomplete' => $autocompleteLine,
    ];

    if (DEBUG) {
        echo PHP_EOL;
    }
}

$illegalSyntaxScore = 0;
foreach ($illegalCharacters as $illegalChar) {
    $illegalSyntaxScore += ILLEGAL_SYNTAX_POINTS[$illegalChar];
}

printf('Illegal syntax score: %d' . PHP_EOL, $illegalSyntaxScore);

$autocompleteScores = [];
foreach ($autocompleteCharacters as $autocompleteLine) {
    if (DEBUG) {
        printf(
            'Line: %25s, Autocomplete: %s' . PHP_EOL,
            $autocompleteLine['line'],
            implode('', $autocompleteLine['autocomplete'])
        );
    }

    $lineScore = 0;
    foreach ($autocompleteLine['autocomplete'] as $char) {
        $lineScore *= 5;
        $lineScore += AUTOCOMPLETE_POINTS[$char];
    }
    $autocompleteScores[] = $lineScore;
}

sort($autocompleteScores);

$autocompleteScore = $autocompleteScores[count($autocompleteScores) / 2];

printf('Autocomplete score: %d' . PHP_EOL, $autocompleteScore);
