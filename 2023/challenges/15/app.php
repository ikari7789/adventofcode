<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

function hashString(string $string): int
{
    $hash = 0;

    foreach (str_split($string) as $char) {
        $hash += ord($char);
        $hash *= 17;
        $hash %= 256;
    }

    return $hash;
}

function hashSequences(array $sequences): array
{
    $hashes = [];

    foreach ($sequences as $sequence) {
        $hashes[] = hashString($sequence);
    }

    return $hashes;
}

function hashMap(array $sequences): array
{
    $hashMap = array_fill(0, 256, []);

    foreach ($sequences as $sequence) {
        preg_match('/^(?P<key>[a-z]+)(?P<operation>=\d+|-)$/', $sequence, $matches);

        [
            'key' => $key,
            'operation' => $operation
        ] = $matches;

        $hash = hashString($matches['key']);

        if ($operation === '-') {
            unset($hashMap[$hash][$key]);

            continue;
        }

        [
            ,
            $value,
        ] = explode('=', $operation);

        $hashMap[$hash][$key] = $value;
    }

    return $hashMap;
}

function sumHashMap(array $hashMap): int
{
    $focusPower = 0;

    foreach ($hashMap as $box => $hashes) {
        $focalSlot = 1;
        foreach ($hashes as $lens => $focalLength) {
            $focusPower += ($box + 1) * $focalSlot * $focalLength;
            ++$focalSlot;
        }
    }

    return $focusPower;
}

$sequences = [];

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $sequences = explode(',', trim($line));
}

$hashes = hashSequences($sequences);

printf('Sum of all hashes: %d' . PHP_EOL, array_sum($hashes));

printf('Focal power of the lens configuration: %d' . PHP_EOL, sumHashMap(hashMap($sequences)));
