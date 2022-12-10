<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Ikari7789\Adventofcode\Year2022\Day9\Rope;
use Ikari7789\Adventofcode\Year2022\Day9\Knot;
use Ikari7789\Adventofcode\Year2022\Day9\Direction;

define('DEBUG', false);
define('INPUT_FILE', __DIR__ . '/example_input_1.txt');

$rope = new Rope([
    new Knot(0, 0),
    new Knot(0, 0),
]);

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    [
        $direction,
        $steps,
    ] = explode(' ', $line);

    $rope->moveHead(Direction::fromString($direction), (int) $steps);
}

$rope->drawVisited();

echo 'Tail visited [' . count(array_unique($rope->visited)) . '] positions' . PHP_EOL;

$rope = new Rope([
    new Knot(0, 0),
    new Knot(0, 0),
    new Knot(0, 0),
    new Knot(0, 0),
    new Knot(0, 0),
    new Knot(0, 0),
    new Knot(0, 0),
    new Knot(0, 0),
    new Knot(0, 0),
]);

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    [
        $direction,
        $steps,
    ] = explode(' ', $line);

    $rope->moveHead(Direction::fromString($direction), (int) $steps);
}

$rope->drawVisited();

echo 'Tail visited [' . count(array_unique($rope->visited)) . '] positions' . PHP_EOL;