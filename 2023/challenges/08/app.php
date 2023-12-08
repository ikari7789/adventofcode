<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

class Nodes
{
    private array $nodes = [];

    public function __construct(
        readonly private array $directions,
        readonly private string $startNode = 'AAA',
        readonly private string $endNode = 'ZZZ',
    ) {
    }

    public function nodes(): array
    {
        return $this->nodes;
    }

    public function addNode(string $name, string $left, string $right): void
    {
        $this->nodes[$name] = [
            'name' => $name,
            'L'    => $left,
            'R'    => $right,
        ];
    }

    public function stepsFromStartToFinish(): int
    {
        $steps = 0;

        $node = $this->nodes[$this->startNode];

        $directionsCount = count($this->directions);
        for ($index = 0; $index < $directionsCount; ++$index) {
            $direction = $this->directions[$index];

            $node = $this->nodes[$this->nodes[$node['name']][$direction]];
            ++$steps;

            if ($node['name'] === $this->endNode) {
                break;
            }

            // Restart the walk
            if ($index === $directionsCount - 1) {
                $index = -1;
            }
        }

        return $steps;
    }
}

class MultiNodes extends Nodes
{
    private array $nodes = [];

    public function __construct(
        readonly private array $directions,
        readonly private string $startNode = 'A',
        readonly private string $endNode = 'Z',
    ) {
    }

    public function nodes(): array
    {
        return $this->nodes;
    }

    public function addNode(string $name, string $left, string $right): void
    {
        $this->nodes[$name] = [
            'name' => $name,
            'L'    => $left,
            'R'    => $right,
        ];
    }

    public function stepsFromStartToFinish(): int
    {
        $startNodes = array_values(
            array_filter(
                array_keys($this->nodes),
                function ($node) {
                    return str_ends_with($node, $this->startNode);
                }
            )
        );

        $steps = array_fill(0, count($startNodes), 0);

        $arrivedAtDestinationCounts = array_fill(0, count($startNodes), 0);

        $nodes     = array_values(array_intersect_key($this->nodes, array_flip($startNodes)));
        $nodeCount = count($nodes);

        $directionsCount = count($this->directions);
        for ($index = 0; $index < $directionsCount; ++$index) {
            $direction = $this->directions[$index];

            for ($nodeIndex = 0; $nodeIndex < $nodeCount; ++$nodeIndex) {
                $nodes[$nodeIndex] = $this->nodes[$this->nodes[$nodes[$nodeIndex]['name']][$direction]];
                ++$steps[$nodeIndex];

                if (str_ends_with($nodes[$nodeIndex]['name'], $this->endNode) && $arrivedAtDestinationCounts[$nodeIndex] === 0) {
                    $arrivedAtDestinationCounts[$nodeIndex] = $steps[$nodeIndex];
                }
            }

            if (! in_array(0, $arrivedAtDestinationCounts, strict: true)) {
                break;
            }

            // Restart the walk
            if ($index === $directionsCount - 1) {
                $index = -1;
            }
        }

        $steps = $arrivedAtDestinationCounts[0];
        for ($index = 1; $index < count($arrivedAtDestinationCounts); ++$index) {
            $steps = gmp_lcm($steps, $arrivedAtDestinationCounts[$index]);
        }

        return gmp_intval($steps);
    }
}


$nodes      = null;
$multiNodes = null;

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    if (preg_match('/^[RL]+$/', $line, $matches)) {
        $nodes      = new Nodes(str_split($line));
        $multiNodes = new MultiNodes(str_split($line));
    } elseif (preg_match('/(?P<name>[A-Z0-9]{3}) = \((?P<left>[A-Z0-9]{3}), (?P<right>[A-Z0-9]{3})\)/', $line, $matches)) {
        $nodes->addNode($matches['name'], $matches['left'], $matches['right']);
        $multiNodes->addNode($matches['name'], $matches['left'], $matches['right']);
    }
}

printf('Steps to reach end: %d' . PHP_EOL, $nodes->stepsFromStartToFinish());
printf('Steps to reach multi end: %d' . PHP_EOL, $multiNodes->stepsFromStartToFinish());