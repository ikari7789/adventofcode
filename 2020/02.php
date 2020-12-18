<?php

// Part One
// Line formatting
// <minimum_character_count>-<maximum_character_count> <character>: <password>
$passwords = new SplFileObject('02.txt');
$validPasswords = 0;
while (! $passwords->eof()) {
    $line = trim($passwords->fgets());

    if (empty($line)) {
        continue;
    }

    [
        $minimumCharacterCount,
        $maximumCharacterCount,
        $character,
        $filler,
        $password
    ] = preg_split('/-| |:/', $line);

    $characterCount = substr_count($password, $character);

    if ($characterCount >= $minimumCharacterCount && $characterCount <= $maximumCharacterCount) {
        ++$validPasswords;
    }
}
unset($passwords);

echo 'Valid password count: '.$validPasswords.PHP_EOL;

// Part 2
// Line formatting
// <character_position_1>-<character_position_2> <character>: <password>
$passwords = new SplFileObject('02.txt');
$validPasswords = 0;
while (! $passwords->eof()) {
    $line = trim($passwords->fgets());

    if (empty($line)) {
        continue;
    }

    [
        $characterPosition1,
        $characterPosition2,
        $character,
        $filler,
        $password
    ] = preg_split('/-| |:/', $line);

    $characterInPosition1 = substr($password, $characterPosition1 - 1, 1);
    $characterInPosition2 = substr($password, $characterPosition2 - 1, 1);

    $hasCharacterInPosition1 = ($characterInPosition1 === $character);
    $hasCharacterInPosition2 = ($characterInPosition2 === $character);

    if (($hasCharacterInPosition1 && ! $hasCharacterInPosition2) || (! $hasCharacterInPosition1 && $hasCharacterInPosition2)) {
        ++$validPasswords;
    }
}
unset($passwords);

echo 'Valid password count: '.$validPasswords.PHP_EOL;
