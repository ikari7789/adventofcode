<?php

$inputFile = '21_test.txt';

/**
 */

$input = new SplFileObject($inputFile);

while (! $input->eof()) {
    $line = $input->fgets();
    $line = trim($line);

    if (empty($line)) {
        continue;
    }
}