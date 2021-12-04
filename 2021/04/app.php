<?php

$input = new SplFileObject('input.txt');

// Get numbers for bingo
$numbers = explode(',', trim($input->fgets()));

$boards       = [];
$currentBoard = -1;
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    if (empty($line)) {
        $boards[] = [];
        ++$currentBoard;

        continue;
    }

    $boards[$currentBoard][] = preg_split('/\s+/', $line);
}

function checkForBingo(array $board, array $numbers)
{
    $height = count($board);
    $width  = count($board[0]);

    // width should equal hight
    assert($width === $height);

    $rowMatches    = array_fill(0, $height, 0);
    $columnMatches = array_fill(0, $width, 0);
    for ($row = 0; $row < count($board); ++$row) {
        for ($column = 0; $column < count($board[$row]); ++$column) {
            $isMatched               = in_array($board[$row][$column], $numbers);
            $rowMatches[$row]       += (int) $isMatched;
            $columnMatches[$column] += (int) $isMatched;
        }
    }

    return in_array(5, $rowMatches) || in_array(5, $columnMatches);
}

function sumOfUnmarked(array $board, array $numbers): int
{
    $sum = 0;
    for ($row = 0; $row < count($board); ++$row) {
        for ($column = 0; $column < count($board[$row]); ++$column) {
            $notMatched = ! in_array($board[$row][$column], $numbers);
            
            $sum += $notMatched ? $board[$row][$column] : 0;
        }
    }

    return $sum;
}

function printBoard($board) {
    foreach ($board as $row) {
        foreach ($row as $column) {
            printf(' %2d', $column);
        }
        print PHP_EOL;
    }
}

$roundsTillBingo = [];
foreach ($boards as $boardNum => $board) {
    $width = count($board);
    for ($round = $width; $round < count($numbers); ++$round) {
        if (checkForBingo($board, array_slice($numbers, 0, $round))) {
            printf('Board %3d BINGO on round %d' . PHP_EOL, $boardNum, $round);
            $roundsTillBingo[] = $round;

            continue 2;
        }
    }
}

$lowestWinningRounds  = min($roundsTillBingo);
$highestWinningRounds = max($roundsTillBingo);
$lowestWinningBoard   = array_search($lowestWinningRounds, $roundsTillBingo);
$highestWinningBoard  = array_search($highestWinningRounds, $roundsTillBingo);

$sumOfUnmarkedNumbers = sumOfUnmarked($boards[$lowestWinningBoard], array_slice($numbers, 0, $lowestWinningRounds));
$numberJustCalled     = $numbers[$lowestWinningRounds - 1];

echo 'Lowest winning board:    ' . $lowestWinningBoard . PHP_EOL;
echo 'Sum of unmarked numbers: ' . $sumOfUnmarkedNumbers . PHP_EOL;
echo 'Number just called:      ' . $numberJustCalled . PHP_EOL;
echo 'Final score:             ' . $sumOfUnmarkedNumbers * $numberJustCalled . PHP_EOL;

$sumOfUnmarkedNumbers = sumOfUnmarked($boards[$highestWinningBoard], array_slice($numbers, 0, $highestWinningRounds));
$numberJustCalled     = $numbers[$highestWinningRounds - 1];

echo 'Highest winning board:   ' . $highestWinningBoard . PHP_EOL;
echo 'Sum of unmarked numbers: ' . $sumOfUnmarkedNumbers . PHP_EOL;
echo 'Number just called:      ' . $numberJustCalled . PHP_EOL;
echo 'Final score:             ' . $sumOfUnmarkedNumbers * $numberJustCalled . PHP_EOL;


$input->rewind();

while (! $input->eof() && $line = trim($input->fgets())) {

}
