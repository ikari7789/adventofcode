<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

define('OPPONENT_ROCK', 'A');
define('OPPONENT_PAPER', 'B');
define('OPPONENT_SCISSORS', 'C');

define('PLAYER_ROCK', 'X');
define('PLAYER_PAPER', 'Y');
define('PLAYER_SCISSORS', 'Z');

define('SHOULD_WIN', 'Z');
define('SHOULD_DRAW', 'Y');
define('SHOULD_LOSE', 'X');

define('LOSS', 0);
define('DRAW', 3);
define('WIN', 6);

function rockPaperScissorsByMove(string $player, string $opponent): int
{
    $score = 0;

    if ($player === PLAYER_ROCK) {
        $score = 1;
    } elseif ($player === PLAYER_PAPER) {
        $score = 2;
    } elseif ($player === PLAYER_SCISSORS) {
        $score = 3;
    }

    if (
        ($player === PLAYER_ROCK && $opponent === OPPONENT_ROCK)
        || ($player === PLAYER_PAPER && $opponent === OPPONENT_PAPER)
        || ($player === PLAYER_SCISSORS && $opponent === OPPONENT_SCISSORS)
    ) {
        return $score + DRAW;
    }

    if (
        ($player === PLAYER_ROCK && $opponent === OPPONENT_SCISSORS)
        || ($player === PLAYER_PAPER && $opponent === OPPONENT_ROCK)
        || ($player === PLAYER_SCISSORS && $opponent === OPPONENT_PAPER)
    ) {
        return $score + WIN;
    }

    return $score + LOSS;
}

function rockPaperScissorsByResult($expectedResult, $opponent): int
{
    $player = '';

    if ($expectedResult === SHOULD_WIN) {
        if ($opponent === OPPONENT_ROCK) {
            $player = PLAYER_PAPER;
        } elseif ($opponent === OPPONENT_PAPER) {
            $player = PLAYER_SCISSORS;
        } elseif ($opponent === OPPONENT_SCISSORS) {
            $player = PLAYER_ROCK;
        }
    }

    if ($expectedResult === SHOULD_DRAW) {
        if ($opponent === OPPONENT_ROCK) {
            $player = PLAYER_ROCK;
        } elseif ($opponent === OPPONENT_PAPER) {
            $player = PLAYER_PAPER;
        } elseif ($opponent === OPPONENT_SCISSORS) {
            $player = PLAYER_SCISSORS;
        }
    }

    if ($expectedResult === SHOULD_LOSE) {
        if ($opponent === OPPONENT_ROCK) {
            $player = PLAYER_SCISSORS;
        } elseif ($opponent === OPPONENT_PAPER) {
            $player = PLAYER_ROCK;
        } elseif ($opponent === OPPONENT_SCISSORS) {
            $player = PLAYER_PAPER;
        }
    }

    return rockPaperScissorsByMove($player, $opponent);
}

$input = new SplFileObject(INPUT_FILE);
$totalScore = 0;
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);
    [$opponent, $player] = explode(' ', $line);

    $totalScore += rockPaperScissorsByMove($player, $opponent);
}

echo "Total score: {$totalScore}" . PHP_EOL;

$input = new SplFileObject(INPUT_FILE);
$totalScore = 0;
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);
    [$opponent, $expectedResult] = explode(' ', $line);

    $totalScore += rockPaperScissorsByResult($expectedResult, $opponent);
}

echo "Total score: {$totalScore}" . PHP_EOL;
