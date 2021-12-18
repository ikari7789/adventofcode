<?php

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('STEPS_TO_TRY', 7);

function ingestInput(string $path)
{
    $input = new SplFileObject($path);
    $line  = trim($input->fgets());

    preg_match('/target area: x=(-?\d+)\.\.(-?\d+), y=(-?\d+)\.\.(-?\d+)/', $line, $matches);

    $lowerRow    = (int) $matches[3];
    $upperRow    = (int) $matches[4];
    $lowerColumn = (int) $matches[1];
    $upperColumn = (int) $matches[2];

    return [
        'lower_row'    => $lowerRow,
        'upper_row'    => $upperRow,
        'lower_column' => $lowerColumn,
        'upper_column' => $upperColumn,
    ];
}

function step(int $row, int $column, array $velocity): array
{
    $column += $velocity[1];
    $row    += $velocity[0];

    // move 1 unit closer to 0
    if ($velocity[1] > 0) {
        --$velocity[1];
    } elseif ($velocity[1] < 0) {
        ++$velocity[1];
    }

    --$velocity[0];

    return [$row, $column, $velocity];
}

function generateTrajectory(int $column, int $row, array $targetArea): array
{
    $velocity   = [$row, $column];
    $trajectory = [[0, 0]];

    $step = 0;
    while (! isPastTarget($trajectory[$step][0], $trajectory[$step][1], $targetArea)) {
        [
            $trajectory[$step + 1][0],
            $trajectory[$step + 1][1],
            $velocity,
        ] = step($trajectory[$step][0], $trajectory[$step][1], $velocity);

        // printf(
        //     '(%d,%d) (%d,%d)' . PHP_EOL,
        //     $trajectory[$step + 1][1],
        //     $trajectory[$step + 1][0],
        //     $velocity[1],
        //     $velocity[0]
        // );

        if (inBounds($trajectory[$step + 1][0], $trajectory[$step + 1][1], $targetArea)) {
            break;
        }

        ++$step;
    }

    return $trajectory;
}

function inBounds(int $row, int $column, array $targetArea): bool
{
    $rowInBounds    = $row >= $targetArea['lower_row'] && $row <= $targetArea['upper_row'];
    $columnInBounds = $column >= $targetArea['lower_column'] && $column <= $targetArea['upper_column'];

    if ($rowInBounds && $columnInBounds) {
        return true;
    }

    return false;
}

function isPastTarget(int $row, int $column, array $targetArea): bool
{
    $rowPastLowerTarget    = $row < $targetArea['lower_row'];
    $rowPastUpperTarget    = $row < $targetArea['upper_row'];
    $columnPastLowerTarget = $column > $targetArea['lower_column'];
    $columnPastUpperTarget = $column > $targetArea['upper_column'];

    //     01234567890123456789012345678901234
    // -04 ....................###########.
    // -05 ...................#TTTTTTTTTTT# (> 30, < 4-)
    // -06 ...................#TTTTTTTTTTT#
    // -07 ...................#TTTTTTTTTTT#
    // -08 ...................#TTTTTTTTTTT#
    // -09 ...................#TTTTTTTTTTT#
    // -10 ...................#TTTTTTTTTTT#
    // -11 ....................###########. (> 30, < -10)
    //                         (> 20, < -10)

    $pastBottom = $rowPastLowerTarget;
    $pastRight  = $columnPastUpperTarget;

    if (!inBounds($row, $column, $targetArea) && ($pastBottom || $pastRight)) {
        return true;
    }

    return false;
}

function highestVelocity(array $trajectories): array
{
    $highestVelocity = [
        'point'    => [0, 0],
        'velocity' => [0, 0],
    ];
    foreach ($trajectories as $trajectory) {
        $start    = array_shift($trajectory);
        $velocity = array_shift($trajectory);
        printf('%d,%d' . PHP_EOL, $velocity[1], $velocity[0]);

        if ($velocity[0] >= $highestVelocity['point'][0]) {
            $highestVelocity = [
                'point'    => $velocity,
                'velocity' => $velocity,
            ];
        }

        foreach ($trajectory as $point) {
            $y = $point[0];
            $x = $point[1];
            if ($y >= $highestVelocity['point'][0]) {
                $highestVelocity = [
                    'point'    => [$y, $x],
                    'velocity' => $velocity,
                ];
            }
        }
    }

    return $highestVelocity;
}

function mapTrajectory(array $trajectory, array $targetArea): void
{
    $maxRow    = 0;
    $maxColumn = 0;
    $minRow    = 0;
    $minColumn = 0;

    $targetAreaCorners = [
        [$targetArea['lower_row'], $targetArea['lower_column']],
        [$targetArea['lower_row'], $targetArea['upper_column']],
        [$targetArea['upper_row'], $targetArea['lower_column']],
        [$targetArea['upper_row'], $targetArea['upper_column']],
    ];

    foreach (array_merge($trajectory, $targetAreaCorners) as $point) {
        $maxRow    = max($maxRow, $point[0]);
        $maxColumn = max($maxColumn, $point[1]);
        $minRow    = min($minRow, $point[0]);
        $minColumn = min($minColumn, $point[1]);
    }

    for ($row = $maxRow; $row >= $minRow; --$row) {
        printf('%03d ', $row);
        for ($column = $minColumn; $column <= $maxColumn; ++$column) {
            $char = '.';

            if (inBounds($row, $column, $targetArea)) {
                $char = 'T';
            }

            for ($index = 0; $index < count($trajectory); ++$index) {
                $point = $trajectory[$index];

                if ($row === $point[0] && $column === $point[1]) {
                    $char = '#';

                    unset($trajectory[$index]);
                    $trajectory = array_values($trajectory);

                    break;
                }
            }

            if ($row === 0 && $column === 0) {
                $char = 'S';
            }

            echo $char;
        }
        echo PHP_EOL;
    }
}

$targetArea = ingestInput(INPUT_FILE);
// dd($targetArea);

$startX = 0;
$endX   = max($targetArea['lower_column'], $targetArea['upper_column']);
$startY = -abs(min($targetArea['lower_row'], $targetArea['upper_row']));
$endY   = abs(min($targetArea['lower_row'], $targetArea['upper_row']));

// mapTrajectory(generateTrajectory(7, 2, $targetArea), $targetArea);
// echo PHP_EOL;

// mapTrajectory(generateTrajectory(6, 3, $targetArea), $targetArea);
// echo PHP_EOL;

// mapTrajectory(generateTrajectory(9, 0, $targetArea), $targetArea);
// echo PHP_EOL;

// mapTrajectory(generateTrajectory(17, -4, $targetArea), $targetArea);
// echo PHP_EOL;

// mapTrajectory(generateTrajectory(6, 9, $targetArea), $targetArea);
// echo PHP_EOL;

$trajectories = [];
for ($x = $startX; $x <= $endX; ++$x) {
    for ($y = $startY; $y <= $endY; ++$y) {
        $trajectory = generateTrajectory($x, $y, $targetArea);

        $steps = count($trajectory) - 1;
        if (isPastTarget($trajectory[$steps][0], $trajectory[$steps][1], $targetArea)) {
            continue;
        }

        $trajectories[] = $trajectory;
    }
}

printf('Number of trajectories: %d' . PHP_EOL, count($trajectories));

$highestVelocity = highestVelocity($trajectories);
printf('Highest velocity (%4d,%4d)' . PHP_EOL, $highestVelocity['velocity'][1], $highestVelocity['velocity'][0]);
printf('Highest point    (%4d,%4d)' . PHP_EOL, $highestVelocity['point'][1], $highestVelocity['point'][0]);