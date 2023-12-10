<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('POSSIBLE_RED_CUBE_COUNT', 12);
define('POSSIBLE_GREEN_CUBE_COUNT', 13);
define('POSSIBLE_BLUE_CUBE_COUNT', 14);

class Game
{
    public array $rounds = [];

    public function __construct(
        readonly int $number,
    ) {}

    public function recordRound(int $red, int $green, int $blue)
    {
        $this->rounds[] = new Round($red, $green, $blue);
    }

    public function power()
    {
        $minRed   = 0;
        $minGreen = 0;
        $minBlue  = 0;

        foreach ($this->rounds as $round) {
            $minRed   = ($round->red > $minRed) ? $round->red : $minRed;
            $minGreen = ($round->green > $minGreen) ? $round->green : $minGreen;
            $minBlue  = ($round->blue > $minBlue) ? $round->blue : $minBlue;
        }

        return $minRed * $minGreen * $minBlue;
    }
}

class Round
{
    public function __construct(
        readonly public int $red,
        readonly public int $green,
        readonly public int $blue,
    ) {}
}

$games = [];

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    // Split by game, rounds
    [
        $game,
        $rounds,
    ] = explode(':', $line);

    // Get game number
    $game = new Game((int) explode(' ', $game)[1]);

    // Record each round
    foreach (explode(';', $rounds) as $round) {
        $cubes = explode(',', $round);

        $round = [
            'red'   => 0,
            'green' => 0,
            'blue'  => 0,
        ];

        foreach ($cubes as $cube) {
            [
                $count,
                $color,
            ] = explode(' ', trim($cube));

            $round[$color] = (int) $count;
        }

        $game->recordRound($round['red'], $round['green'], $round['blue']);
    }

    $games[] = $game;
}

$sum = 0;
foreach ($games as $game) {
    // Skip to next game if this one had any rounds which
    // aren't able to hit the possible counts
    foreach ($game->rounds as $round) {
        if (
            $round->red > POSSIBLE_RED_CUBE_COUNT
            || $round->green > POSSIBLE_GREEN_CUBE_COUNT
            || $round->blue > POSSIBLE_BLUE_CUBE_COUNT
        ) {
            continue 2;
        }
    }

    $sum += $game->number;
}

printf('Total sum of possible game numbers: %d' . PHP_EOL, $sum);

$sum = 0;
foreach ($games as $game) {
    $sum += $game->power();
}

printf('Total sum of game powers: %d' . PHP_EOL, $sum);
