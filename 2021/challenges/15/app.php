<?php

/**
 * Ref: https://searchcode.com/codesearch/view/16879020/
 */

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', false);
// define('INPUT_FILE', __DIR__ . '/example_input.txt');
// define('ENDPOINT_1', '009009');
// define('ENDPOINT_2', '049049');
define('INPUT_FILE', __DIR__ . '/input.txt');
define('ENDPOINT_1', '099099');
define('ENDPOINT_2', '499499');
define('MAP_MULTIPLYER', 5);
define('INFINITY', PHP_INT_MAX);

function ingestInput(SplFileObject $input): array
{
    $riskLevels = [];

    while (! $input->eof() && $line = trim($input->fgets())) {
        $riskLevels[] = array_map('intval', str_split($line));
    }

    return $riskLevels;
}

function printMap(array $map): void
{
    foreach ($map as $columns) {
        foreach ($columns as $column) {
            echo $column;
        }
        echo PHP_EOL;
    }
}

function multiplyMap(array $riskLevels): array
{
    $width  = count($riskLevels);
    $height = count($riskLevels);
    for ($row = 0; $row < $width; ++$row) {
        for ($column = 0; $column < $height; ++$column) {
            $baseValue = $riskLevels[$row][$column];

            // (00,00) (10,00) (20,00) (30,00) (40,00)
            // (00,10) (10,10) (20,10) (30,10) (40,10)
            // (00,20) (10,20) (20,20) (30,20) (40,20)
            // (00,30) (10,30) (20,30) (30,30) (40,30)
            // (00,40) (10,40) (20,40) (30,40) (40,40)

            for ($rowMultiplier = 0; $rowMultiplier < MAP_MULTIPLYER; ++$rowMultiplier) {
                for ($columnMultiplier = 0; $columnMultiplier < MAP_MULTIPLYER; ++$columnMultiplier) {
                    $newRow    = $row + ($width * $rowMultiplier);
                    $newColumn = $column + ($height * $columnMultiplier);

                    if ($newRow < $width && $newColumn < $height) {
                        continue;
                    }

                    if ($columnMultiplier === 0) {
                        $newValue = $riskLevels[$newRow - $width][$newColumn];
                    } else {
                        $newValue = $riskLevels[$newRow][$newColumn - $height];
                    }

                    // $newValue = $baseValue + $columnMultiplier;
                    ++$newValue;

                    if ($newValue > 9) {
                        $newValue -= 9;
                    }

                    $riskLevels[$newRow][$newColumn] = $newValue;
                }
            }
        }
    }

    ksort($riskLevels);

    for ($row = 0; $row < count($riskLevels); ++$row) {
        ksort($riskLevels[$row]);
    }

    return $riskLevels;
}

function generateGrid(array $riskLevels): array
{
    $grid = [];

    for ($row = 0; $row < count($riskLevels); ++$row) {
        for ($column = 0; $column < count($riskLevels[$row]); ++$column) {
            $point      = sprintf('%03d%03d', $row, $column);
            $upPoint    = sprintf('%03d%03d', $row - 1, $column);
            $rightPoint = sprintf('%03d%03d', $row, $column + 1);
            $downPoint  = sprintf('%03d%03d', $row + 1, $column);
            $leftPoint  = sprintf('%03d%03d', $row, $column - 1);

            $grid[$point] = [];

            if (isset ($riskLevels[$row - 1][$column])) {
                $grid[$point][$upPoint] = $riskLevels[$row - 1][$column];
            }

            if (isset($riskLevels[$row][$column + 1])) {
                $grid[$point][$rightPoint] = $riskLevels[$row][$column + 1];
            }

            if (isset ($riskLevels[$row + 1][$column])) {
                $grid[$point][$downPoint] = $riskLevels[$row + 1][$column];
            }

            if (isset ($riskLevels[$row][$column - 1])) {
                $grid[$point][$leftPoint] = $riskLevels[$row][$column - 1];
            }
        }
    }

    return $grid;
}

class MinPriorityQueue extends SplPriorityQueue {
  public function compare($a, $b): int {
      return parent::compare($b, $a); //inverse the order
  }
}

function dijkstra(array $graph, $initialVertex) {
  $distance = array();
  
  foreach (array_keys($graph) as $v) {
      $distance[$v]  = INFINITY; 
  }
  
  $distance[$initialVertex] = 0;
  
  $nonOptimizedVertices = new MinPriorityQueue();
  $nonOptimizedVertices->insert($initialVertex, $distance[$initialVertex]);
  
  while(!$nonOptimizedVertices->isEmpty()) {
      $u = $nonOptimizedVertices->extract();
      if ($distance[$u] == INFINITY) {
          return false; //All the other elements are inacessible
      }
      foreach($graph[$u] as $neighbor => $edgeWeight) {
          $newDistance = $distance[$u] + $edgeWeight;
          if ($newDistance < $distance[$neighbor]) {
              $distance[$neighbor] = $newDistance;
              $nonOptimizedVertices->insert($neighbor,$distance[$neighbor]); 
          }
      }

  }
  return $distance;
}

function generatePaths(array $riskLevels, int $row, int $column, array &$paths = [], int $risk = 0)
{
    if (!($row === 0 && $column === 0)) {
        $risk += $riskLevels[$row][$column];
    }

    if ($row === count($riskLevels) - 1 && $column === count($riskLevels[$row]) - 1) {
        if (! isset($paths[$risk])) {
            $paths[$risk] = 0;
        }

        ++$paths[$risk];

        if (DEBUG) {
            printf('Risk: %d' . PHP_EOL, $risk);
        }
    }

    if ($column + 1 < count($riskLevels[$row])) {
        generatePaths($riskLevels, $row, $column + 1, $paths, $risk);
    }

    if ($row + 1 < count($riskLevels)) {
        generatePaths($riskLevels, $row + 1, $column, $paths, $risk);
    }
}

$input      = new SplFileObject(INPUT_FILE);
$riskLevels = ingestInput($input);

$grid          = generateGrid($riskLevels);
$shortestPaths = dijkstra($grid, '000000');

printf('Lowest total: %d' . PHP_EOL, $shortestPaths[ENDPOINT_1]);

$riskLevels    = multiplyMap($riskLevels);
$grid          = generateGrid($riskLevels);
$shortestPaths = dijkstra($grid, '000000');

printf('Lowest total after multiplying: %d' . PHP_EOL, $shortestPaths[ENDPOINT_2]);