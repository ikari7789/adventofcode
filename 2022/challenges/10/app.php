<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

define('NOOP', 'noop');
define('ADDX', 'addx');

define('X_REGISTER_START_VALUE', 1);
define('SCREEN_WIDTH', 40);
define('DARK_PIXEL', '.');
define('LIT_PIXEL', '#');

function drawLine(int $position): array
{
    $screenLine = array_fill(0, SCREEN_WIDTH, DARK_PIXEL);

    if (isset($screenLine[$position - 1])) {
        $screenLine[$position - 1] = LIT_PIXEL;
    }

    if (isset($screenLine[$position])) {
        $screenLine[$position] = LIT_PIXEL;
    }

    if (isset($screenLine[$position + 1])) {
        $screenLine[$position + 1] = LIT_PIXEL;
    }

    return $screenLine;
}

$xRegister = X_REGISTER_START_VALUE;
$cycles    = [];

$signalStrengths = [];

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    $command = explode(' ', $line);

    array_push($cycles, ...$command);
}

for ($cycle = 0; $cycle < count($cycles); $cycle++) {
    $command = $cycles[$cycle];

    if ($cycle === 19 || (($cycle - 19) % SCREEN_WIDTH) === 0) {
        $signalStrengths[$cycle] = ($cycle + 1) * $xRegister;
    }

    if (is_numeric($command)) {
        $xRegister += $command;
    }
}

printf('Sum of signal strengths: %d' . PHP_EOL, array_sum($signalStrengths));

echo 'Screen output:' . PHP_EOL;

$xRegister   = X_REGISTER_START_VALUE;
$screenLine  = drawLine($xRegister);

for ($cycle = 0; $cycle < count($cycles); $cycle++) {
    $command = $cycles[$cycle];

    echo $screenLine[$cycle % SCREEN_WIDTH];

    if (is_numeric($command)) {
        $xRegister += $command;
        $screenLine = drawLine($xRegister);
    }

    if (($cycle + 1) % SCREEN_WIDTH === 0) {
        echo PHP_EOL;
        $screenLine = drawLine($xRegister);
    }
}
