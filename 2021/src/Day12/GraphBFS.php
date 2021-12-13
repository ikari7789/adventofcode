<?php

namespace Ikari7789\Adventofcode\Year2021\Day12;

use SplQueue;

class GraphBFS
{
    public function __construct(
        private array $graph = [],
    ) {

    }

    public function addEdge(string $a, string $b): void
    {
        if (! isset($this->graph[$a])) {
            $this->graph[$a] = [];
        }

        $this->graph[$a][] = $b;
    }

    public function printAllPaths(string $start, string $end): void
    {
        $paths = $this->bfsPath($start, $end);

        foreach ($paths as $path) {
            dump($path);
            // echo implode(',', $path) . PHP_EOL;
        }
    }

    public function bfsPath(string $start, string $end): array
    {
        $queue = new SplQueue();

        $queue->enqueue([$start]);
        $visited = [$start];

        while ($queue->count() > 0) {
            $path = $queue->dequeue();

            // Get the last node on the path
            // so we can check if we're at the end
            $node = $path[count($path) - 1];

            if ($node === $end) {
                return $path;
            }

            foreach ($this->graph[$node] as $neighbor) {
                if (! in_array($neighbor, $visited)) {
                    $visited[] = $neighbor;

                    // Build new path appending the neighbor then and enqueu it
                    $newPath   = $path;
                    $newPath[] = $neighbor;

                    $queue->enqueue($newPath);
                }
            }
        }

        return [s];
    }
}