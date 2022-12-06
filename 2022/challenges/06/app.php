<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('START_OF_PACKET_SIZE', 4);
define('START_OF_MESSAGE_SIZE', 14);

function readInput(string $inputFile) {
    $datastream = '';

    $input = new SplFileObject($inputFile);
    while (! $input->eof() && $line = $input->fgets()) {
        $datastream = trim($line);
    }

    return $datastream;
}

function findPacket(string $datastream, int $markerSize, ?int $start = null): int
{
    if ($start === null) {
        $start = $markerSize;
    }

    $position = $start - $markerSize;
    while ($position <= strlen($datastream) - $markerSize) {
        $marker = substr($datastream, $position, $markerSize);

        if (count(array_unique(str_split($marker))) === $markerSize) {
            return $position + $markerSize;
        }

        $position++;
    }

    return -1;
}

$datastream = readInput(INPUT_FILE);
dump(findPacket($datastream, START_OF_PACKET_SIZE));
dump(findPacket($datastream, START_OF_MESSAGE_SIZE));
