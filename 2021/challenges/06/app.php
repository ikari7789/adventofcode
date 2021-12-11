<?php

define('DEBUG', false);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('DAYS_TILL_BIRTH', 6);
define('INITIAL_DAYS_TILL_BIRTH', 8);
define('DAYS_TO_SIMULATE', 80);

$input = new SplFileObject(INPUT_FILE);

$fish = explode(',', trim($input->fgets()));

if (DEBUG) {
    printf('Initial state: %s' . PHP_EOL, implode(',', $fish));
}

for ($day = 1; $day <= DAYS_TO_SIMULATE; ++$day) {
    $numberOfFish = count($fish);
    for ($index = 0; $index < $numberOfFish; ++$index) {
        $daysTillBirth = $fish[$index];

        if ($fish[$index] === 0) {
            $fish[]       = INITIAL_DAYS_TILL_BIRTH;
            $fish[$index] = DAYS_TILL_BIRTH;

            continue;
        }

        $fish[$index] -= 0b1;
    }

    $days = 'days:';
    if ($day === 1) {
        $days = 'day: ';
    }

    $countOfFish = count($fish);
    if (DEBUG) {
        $countOfFish = implode(',', $fish);
    }

    printf('After %2d %s %s' . PHP_EOL, $day, $days, $countOfFish);
}

printf('Total fish: %d' . PHP_EOL, count($fish));

$input->rewind();

while (! $input->eof() && $line = trim($input->fgets())) {

}
