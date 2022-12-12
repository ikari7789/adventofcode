<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Ikari7789\Adventofcode\Year2022\Day11\Monkey;

define('DEBUG', false);
define('INPUT_FILE', __DIR__ . '/input.txt');

define('BOREDOM_DIVISOR', 3);
define('ROUNDS', 10000);

$monkeys = [];

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);
    if (preg_match('/^Monkey (?P<monkey>\d+):$/', $line, $matches)) {
        $monkey = new Monkey();
    }

    if (preg_match('/^Starting items: /', $line)) {
        $monkey->items = array_map(function ($item) {
            return gmp_init($item);
        }, explode(', ', substr($line, 16)));
    }

    if (preg_match('/^Operation: new = old /', $line)) {
        $value = substr($line, 23);
        $value = $value === 'old' ? $value : gmp_init($value);
        $monkey->operation = [
            'operator' => substr($line, 21, 1),
            'value'    => $value,
        ];
    }

    if (preg_match('/^Test: divisible by /', $line)) {
        $monkey->testDivisor = gmp_init(substr($line, 19));
    }

    if (preg_match('/^If true: throw to monkey /', $line)) {
        $monkey->ifTrue = (int) substr($line, 25);
    }

    if (preg_match('/^If false: throw to monkey /', $line)) {
        $monkey->ifFalse = (int) substr($line, 26);
    }

    if (empty($line)) {
        $monkeys[] = $monkey;
    }
}
$monkeys[] = $monkey;

// if (DEBUG) dump($monkeys);

$zero = gmp_init('0');

for ($round = 0; $round < ROUNDS; $round++) {
    printf('Round %d' . PHP_EOL, $round);
    foreach ($monkeys as $number => $monkey) {
        if (DEBUG) printf('Monkey %d:' . PHP_EOL, $number);

        if (empty($monkey->items)) {
            if (DEBUG) printf('  No items to throw.' . PHP_EOL);
        }

        while (! empty($monkey->items)) {
            $worryLevel = array_shift($monkey->items);

            $monkey->inspections++;

            if (DEBUG) printf('  Monkey inspects an item with a worry level of %d.' . PHP_EOL, $worryLevel);

            $value = $monkey->operation['value'];

            if ($value === 'old') {
                $value = $worryLevel;
            }

            if ($monkey->operation['operator'] === '+') {
                $worryLevel = gmp_add($worryLevel, $value);
                if (DEBUG) printf('    Worry level increases by %d to %d.' . PHP_EOL, $value, $worryLevel);
            }

            if ($monkey->operation['operator'] === '*') {
                $worryLevel = gmp_mul($worryLevel, $value);
                if (DEBUG) printf('    Worry level is multiplied by %d to %d.' . PHP_EOL, $value, $worryLevel);
            }

            // $worryLevel = (int) floor($worryLevel / BOREDOM_DIVISOR);

            // if (DEBUG) printf('    Monkey gets bored with item. Worry level is divided by %d to %d.' . PHP_EOL, BOREDOM_DIVISOR, $worryLevel);

            $recipient = $monkey->ifFalse;
            if (gmp_cmp(gmp_mod($worryLevel, $monkey->testDivisor), $zero) === 0) {
                if (DEBUG) printf('    Current worry level is divisible by %d.' . PHP_EOL, $monkey->testDivisor);
                $recipient = $monkey->ifTrue;
            } else {
                if (DEBUG) printf('    Current worry level is not divisible by %d.' . PHP_EOL, $monkey->testDivisor);
            }

            $monkeys[$recipient]->items[] = $worryLevel;

            if (DEBUG) printf('    Item with worry level %d is thrown to monkey %d.' . PHP_EOL, $worryLevel, $recipient);
        }
    }
    echo PHP_EOL;
}

// if (DEBUG) dump($monkeys);

$inspections = array_map(function ($item) {
    return $item->inspections;
}, $monkeys);

rsort($inspections);

dump($inspections);

printf('The level of monkey business: %d' . PHP_EOL, ($inspections[0] * $inspections[1]));
