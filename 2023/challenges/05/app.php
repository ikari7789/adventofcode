<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

function addMappingToSource(array $almanac, string $source, string $mapping): array
{
    if (!isset($almanac[$source])) {
        $almanac[$source] = [
            'maps_to' => '',
            'values'  => [],
            'ranges'  => [],
        ];
    }

    $almanac[$source]['maps_to'] = $mapping;

    return $almanac;
}

function addRangeToSource(
    array $almanac,
    string $source,
    int $destinationRangeStart,
    int $sourceRangeStart,
    int $rangeLength
): array {
    $almanac[$source]['ranges'][] = [
        'destination_range_start' => $destinationRangeStart,
        'source_range_start'      => $sourceRangeStart,
        'range_length'            => $rangeLength,
    ];

    return $almanac;
}

function addSeeds(array $almanac, array $seeds): array
{
    $almanac['seed']['values'] = $seeds;

    return $almanac;
}

function calculateMapping(array $almanac, int $value, ?string $source = 'seed'): int
{
    if (!isset($almanac[$source])) {
        return $value;
    }

    $mappedValue = $value;
    foreach ($almanac[$source]['ranges'] as $range) {
        if ($value < $range['source_range_start']) {
            // value should translate directly
        } elseif ($value > $range['source_range_start'] + $range['range_length']) {
            // value should translate directly
        } else {
            // value should be mapped because it's inbetween the start/end range
            $mappedValue = $range['destination_range_start'] + ($value - $range['source_range_start']);

            break;
        }
    }

    return calculateMapping($almanac, $mappedValue, $almanac[$source]['maps_to']);
}

function calculateMappings(array $almanac, string $source = 'seed'): array
{
    $mappings = [];
    foreach ($almanac[$source]['values'] as $value) {
        $mappings[] = calculateMapping($almanac, $value);
    }

    return $mappings;
}

function calculateLowestMappingForSeedRanges(array $almanac, string $source = 'seed'): int
{
    $lowestMapping = null;

    foreach (array_chunk($almanac[$source]['values'], 2) as $chunk) {
        for ($value = $chunk[0]; $value <= $chunk[0] + $chunk[1]; ++$value) {
            $mapping = calculateMapping($almanac, $value);

            if ($lowestMapping === null) {
                $lowestMapping = $mapping;
            }

            $lowestMapping = min($lowestMapping, $mapping);
        }
    }

    return $lowestMapping;
}

$almanac      = [];
$activeSource = null;

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    if (preg_match('/^(?P<source>[a-z]+)(?:-to-(?P<destination>[a-z]+) map)?:$/', $line, $matches)) {
        // Match new map
        $almanac = addMappingToSource($almanac, $matches['source'], $matches['destination']);

        $activeSource = $matches['source'];
    } elseif (preg_match('/^(?P<destination_range_start>\d+) (?P<source_range_start>\d+) (?<range_length>\d+)$/', $line, $matches)) {
        // Match mappings
        $almanac = addRangeToSource(
            $almanac,
            $activeSource,
            (int) $matches['destination_range_start'],
            (int) $matches['source_range_start'],
            (int) $matches['range_length']
        );
    } elseif (preg_match('/^seeds:( \d+)+?$/', $line, $matches)) {
        // Match mappings
        $almanac = addSeeds($almanac, array_map(fn($item) => (int) $item, array_filter(explode(' ', explode(':', $line)[1]))));
    } else {
        // Empty or invalid line
        continue;
    }
}

$mappings = calculateMappings($almanac);

printf('Lowest location: %d' . PHP_EOL, min($mappings));

$lowestMapping = calculateLowestMappingForSeedRanges($almanac);

printf('Lowest location when seeds are mappings: %d' . PHP_EOL, $lowestMapping);
