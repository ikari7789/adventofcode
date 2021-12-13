<?php

require __DIR__ . '/../../vendor/autoload.php';

use Ikari7789\Adventofcode\Year2021\Day12\Graph;
use Ikari7789\Adventofcode\Year2021\Day12\LinkedList;
use Ikari7789\Adventofcode\Year2021\Day12\Node;

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/example_input1.txt');

function findPaths(array $caves, string $start = 'start', array $visited = [])
{
    echo $start . ',';

    $visited[] = $start;

    if ($start === 'end') {
        return $visited;
    }

    foreach ($caves[$start] as $path) {
        if (in_array($path, $visited)) {
            continue;
        }

        $visited = findPaths($caves, $path, $visited);
    }

    // Capitalized caves can be revisited
    if (! preg_match('/[A-Z]+/', $start) && in_array($start, $visited)) {
        return $visited;
    }


}

$caves = [];

$input = new SplFileObject(INPUT_FILE);

while (! $input->eof() && $line = trim($input->fgets())) {
    [
        $pointA,
        $pointB,
    ] = explode('-', $line);

    // Always ensure "start" is pointA
    if ($pointB === 'start') {
        $temp   = $pointB;
        $pointB = $pointA;
        $pointA = $temp;
    }

    // Always ensure "end" is pointB
    if ($pointA === 'end') {
        $temp   = $pointA;
        $pointA = $pointB;
        $pointB = $temp;
    }

    // Add possible paths to array
    $paths = [
        $pointA => $pointB,
    ];

    // If pointB is all caps, it can be revisited
    // Add it to the paths, but don't go back to start
    $canBeRevisited = (
        preg_match('/[A-Z]+/', $pointA)
        || preg_match('/[A-Z]+/', $pointB)
    );

    if ($canBeRevisited) {// && $pointA !== 'start' && $pointB !== 'end') {
        $paths[$pointB] = $pointA;
    }

    foreach ($paths as $start => $end) {
        if (! isset($caves[$start])) {
            $caves[$start] = [];
        }

        $caves[$start][] = $end;
    }
}

$graph = new Graph();
foreach ($caves as $start => &$paths) {
    foreach ($paths as $path) {
        // Remove any paths which cannot work
        if ($path !== 'end' && ! isset($caves[$path])) {
            continue;
        }

        $graph->addEdge($start, $path);
    }
}

dump($graph);
$graph->printAllPaths('start', 'end');