<?php

// Ref: https://github.com/artesea/advent-of-code/blob/main/2021/06b.php

define('DEBUG', false);
define('INPUT_FILE', 'input.txt');
define('DAYS_TILL_BIRTH', 6);
define('INITIAL_DAYS_TILL_BIRTH', 8);
define('DAYS_TO_SIMULATE', 256);

$input = new SplFileObject(INPUT_FILE);

$fish = explode(',', trim($input->fgets()));

$count = [];

foreach ($fish as $f) {
    $count[$f] = ($count[$f] ?? 0) + 1;
}

for ($d = 1; $d <= DAYS_TO_SIMULATE; $d++) {
    $temp = [];

    for ($i = 1; $i <= INITIAL_DAYS_TILL_BIRTH; $i++) {
        $temp[$i - 1] = $count[$i] ?? 0;
    }

    $temp[DAYS_TILL_BIRTH]        += $count[0] ?? 0;
    $temp[INITIAL_DAYS_TILL_BIRTH] = $count[0] ?? 0;
    $count                         = $temp;
}

printf('Total fish: %d' . PHP_EOL, array_sum($count));
