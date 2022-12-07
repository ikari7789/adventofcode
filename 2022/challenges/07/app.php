<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Ikari7789\Adventofcode\Year2022\Day7\Directory;
use Ikari7789\Adventofcode\Year2022\Day7\File;

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

define('COMMAND_CD', 'cd');
define('COMMAND_LS', 'ls');

define('ROOT', '/');
define('UP_DIRECTORY', '..');

define('MAX_DIRECTORY_SIZE', 100000);
define('DISK_SIZE', 70000000);
define('FREE_DISK_SIZE_REQUIRED', 30000000);

function printTree(Directory|File $item, int $level = 0): void
{
    echo str_repeat('  ', $level) . '- ' . $item . PHP_EOL;

    if ($item instanceof File) {
        return;
    }

    foreach ($item->children as $child) {
        printTree($child, $level + 1);
    }
}

function directorySizesUnder(Directory|File $item, int $maxSize = 0): array
{
    $sizes = [];

    if ($item instanceof File) {
        return $sizes;
    }

    $size = $item->size();
    if ($size <= $maxSize) {
        $sizes[] = $size;
    }

    foreach ($item->children as $child) {
        $sizes = array_merge($sizes, directorySizesUnder($child, $maxSize));
    }

    return $sizes;
}

$rootDirectory    = new Directory('/');
$currentDirectory = $rootDirectory;

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    if (preg_match('/^\$ cd (?P<directory>.*)$/', $line, $matches)) {
        if ($matches['directory'] === ROOT) {
            $currentDirectory = $rootDirectory;
        } elseif ($matches['directory'] === UP_DIRECTORY) {
            $currentDirectory = $currentDirectory->parent;
        } else {
            $currentDirectory = $currentDirectory->children[$matches['directory']];
        }
    } elseif (preg_match('/^\$ ls$/', $line, $matches)) {
        // skip to next loop iteration
    } elseif (preg_match('/^dir (?P<directory>.*)$/', $line, $matches)) {
        $currentDirectory->addDirectory($matches['directory']);
    } elseif (preg_match('/^(?P<size>\d+) (?P<file>.*)$/', $line, $matches)) {
        $currentDirectory->addFile($matches['file'], (int) $matches['size']);
    }
}

printTree($rootDirectory);

$directorySizesUnder = directorySizesUnder($rootDirectory, MAX_DIRECTORY_SIZE);

echo 'Sum of directories under ' . MAX_DIRECTORY_SIZE . ': ' . array_sum($directorySizesUnder) . PHP_EOL;

$freeSpace = DISK_SIZE - $rootDirectory->size();
$requiredSpaceToFree = FREE_DISK_SIZE_REQUIRED - $freeSpace;

$directorySizes = directorySizesUnder($rootDirectory, DISK_SIZE);

sort($directorySizes);

$directorySizes = array_values(array_filter($directorySizes, function ($size) use ($requiredSpaceToFree) {
    return $size > $requiredSpaceToFree;
}));

dd($directorySizes[0]);