<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

require_once 'src/Comparator.php';
require_once 'src/Part.php';
require_once 'src/Parts.php';
require_once 'src/PartType.php';
require_once 'src/Sorter.php';
require_once 'src/Workflow.php';
require_once 'src/WorkflowRule.php';
require_once 'src/Workflows.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

$workflows = new Workflows();
$parts = new Parts();

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    // Processing a workflow
    if (preg_match('/^[a-z]+\{/', $line)) {
        // Trim off trailing } from rules
        $line = rtrim($line, '}');

        [
            $name,
            $rules,
        ] = explode('{', $line);

        $rules = explode(',', $rules);

        $fallback = array_pop($rules);

        for ($index = 0; $index < count($rules); ++$index) {
            preg_match('/(?P<part_type>[xmas])(?P<comparator>[<>])(?P<amount>\d+):(?P<fallback>[a-zAR]+)/', $rules[$index], $matches);

            $rules[$index] = new WorkflowRule(
                PartType::from($matches['part_type']),
                Comparator::from($matches['comparator']),
                (int) $matches['amount'],
                $matches['fallback'],
            );
        }

        $workflows->addWorkflow(new Workflow($name, $rules, $fallback));

        continue;
    }

    // Processing a part
    if (preg_match('/^\{/', $line)) {
        $line = trim($line, '{}');

        [
            $cool,
            $musical,
            $aerodynamic,
            $shiny
        ] = array_map(fn($score) => (int) explode('=', $score)[1], explode(',', $line));

        $parts->addPart(new Part($cool, $musical, $aerodynamic, $shiny));
    }
}

// echo $workflows;
// echo PHP_EOL;
// echo PHP_EOL;
// echo $parts;
// echo PHP_EOL;

$sorter = new Sorter($parts, $workflows);

// dump($sorter->accepted());
// dump($sorter->rejected());

printf('Sum of accepted parts: %d' . PHP_EOL, $sorter->acceptedSum());
echo PHP_EOL;

echo 'Creating all possible parts...' . PHP_EOL;
$parts = new Parts();
for ($cool = 1; $cool <= 4000; ++$cool) {
    for ($musical = 1; $musical <= 4000; ++$musical) {
        for ($aerodynamic = 1; $aerodynamic <= 4000; ++$aerodynamic) {
            for ($shiny = 1; $shiny <= 4000; ++$shiny) {
                $parts->addPart(new Part($cool, $musical, $aerodynamic, $shiny));
            }
        }
    }
}

echo 'Sorting parts...' . PHP_EOL;
$sorter = new Sorter($parts, $workflows);

printf('Total distinct acceptable parts: %d' . PHP_EOL, count($sorter->accepted()));
