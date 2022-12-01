<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Ikari7789\Adventofcode\Year2022\Day1\Elf;

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

$elves = [];

$input = new SplFileObject(INPUT_FILE);
$elf = new Elf();
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    if (empty($line)) {
        array_push($elves, $elf);
        $elf = new Elf();

        continue;
    }

    array_push($elf->inventory, (int) $line);
}

array_push($elves, $elf);

$maxCalories = 0;
foreach ($elves as $elf) {
    $elfCalories = $elf->totalCalories();
    if ($elfCalories > $maxCalories) {
        $maxCalories = $elfCalories;
    }
}

echo 'Max calories: ' . $maxCalories . PHP_EOL;

$totalCaloriesPerElf = [];
foreach ($elves as $elf) {
    array_push($totalCaloriesPerElf, $elf->totalCalories());
}
rsort($totalCaloriesPerElf);

echo 'Calories of top 3: ' . ($totalCaloriesPerElf[0] + $totalCaloriesPerElf[1] + $totalCaloriesPerElf[2]) . PHP_EOL;
