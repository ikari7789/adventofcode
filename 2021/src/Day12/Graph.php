<?php

namespace Ikari7789\Adventofcode\Year2021\Day12;

class Graph
{
    private array $graph = [];

    private array $pathLists = [];

    public function addEdge(string $a, string $b): void
    {
        if (! isset($this->graph[$a])) {
            $this->graph[$a] = [];
        }

        $this->graph[$a][] = $b;
    }

    public function printAllPaths(string $start, string $end): void
    {
        $pathLists = $this->getAllPaths($start, $end);

        foreach ($pathLists as $pathList) {
            echo implode(',', $pathList) . PHP_EOL;
        }
    }

    public function getAllPaths(string $start, string $end): array
    {
        $this->pathLists = [];

        // Determine all unique path points
        $isVisited = array_fill_keys(array_keys($this->graph), false);

        $pathLists = [];
        
        $pathList   = [];
        $pathList[] = $start;
        $this->getAllPathsRecursive($start, $end, $isVisited, $pathList);

        // Return only unique entries
        $this->pathLists = array_map(function ($pathList) {
            return implode(',', $pathList);
        }, $this->pathLists);
        $this->pathLists = array_unique($this->pathLists);
        $this->pathLists = array_map(function ($pathList) {
            return explode(',', $pathList);
        }, $this->pathLists);

        return $this->pathLists;
    }

    public function getAllPathsRecursive(string $start, string $end, array $isVisited, array $pathList): void
    {
        if ($start === $end) {
            $this->pathLists[] = $pathList;

            return;
        }

        if (! preg_match('/[A-Z]+/', $start)) {
            $isVisited[$start] = true;
        }

        foreach ($this->graph[$start] as $vertex) {
            if (! $isVisited[$vertex]) {
                $pathList[] = $vertex;
                $this->getAllPathsRecursive($vertex, $end, $isVisited, $pathList);

                unset($pathList[$vertex]);
            }
        }

        $isVisited[$start] = false;
    }
}