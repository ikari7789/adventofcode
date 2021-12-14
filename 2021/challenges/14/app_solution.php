<?php

/**
 * Courtesy: https://github.com/mintopia/aoc-2021/blob/develop/src/Day14.php
 */

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', false);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('NUMBER_OF_STEPS_PART_1', 10);
define('NUMBER_OF_STEPS_PART_2', 40);

$input = new SplFileObject(INPUT_FILE);

$template = '';
$rules = [];

$lineCount = 0;
while (! $input->eof() && $line = $input->fgets()) {
    ++$lineCount;
    $line = trim($line);

    if (preg_match('/^[A-Z]+$/', $line)) {
        $template = $line;

        continue;
    }

    if (preg_match('/^[A-Z]{2} -> [A-Z]{1}$/', $line)) {
        [
            $pair,
            $insertion,
        ] = explode(' -> ', $line);
        $rules[$pair] = $insertion;

        continue;
    }
}

function splitChain(string $chain): array
{
    $pairs = [];

    for ($i = 0; $i < strlen($chain) - 1; ++$i) {
        $pair  = substr($chain, $i, 2);
        $pairs = addToKey($pairs, $pair);
    }

    return $pairs;
}

function addToKey(array $arr, string $key, int $value = 1): array
{
    if (! array_key_exists($key, $arr)) {
        $arr[$key] = 0;
    }

    $arr[$key] += $value;

    return $arr;
}

function scoreChain(string $template, array $rules, int $iterations): int
{
    $charCount = [];
    $pairs = splitChain($template);

    foreach (str_split($template) as $char) {
        $charCount = addToKey($charCount, $char);
    }

    for ($i = 0; $i < $iterations; $i++) {
        $newPairs = $pairs;
        foreach ($rules as $str => $rep) {
            if (! array_key_exists($str, $pairs)) {

                continue;
            }

            $count = $pairs[$str];

            $charCount = addToKey($charCount, $rep, $count);
            $newPairs  = addToKey($newPairs, $str, $count * -1);

            $chars    = str_split($str);
            $newPairs = addToKey($newPairs, "{$chars[0]}{$rep}", $count);
            $newPairs = addToKey($newPairs, "{$rep}{$chars[1]}", $count);
        }

        $pairs = $newPairs;
    }

    sort($charCount);

    return end($charCount) - $charCount[0];
}

printf('Step %d - Most common minus least common: %d' . PHP_EOL, NUMBER_OF_STEPS_PART_1, scoreChain($template, $rules, NUMBER_OF_STEPS_PART_1));
printf('Step %d - Most common minus least common: %d' . PHP_EOL, NUMBER_OF_STEPS_PART_2, scoreChain($template, $rules, NUMBER_OF_STEPS_PART_2));
