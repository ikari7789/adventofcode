<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

class Scratchcard
{
    protected int $copies = 1;

    protected ?int $matches = null;

    protected ?int $score = null;

    public function __construct(
        protected int $cardNumber,
        protected array $numbers,
        protected array $winningNumbers,
    ) {
    }

    public function getCardNumber(): int
    {
        return $this->cardNumber;
    }

    public function matches(): int
    {
        if ($this->matches !== null) {
            return $this->matches;
        }
        
        $this->matches = count(array_intersect($this->numbers, $this->winningNumbers));

        return $this->matches;
    }

    public function addCopy(): void
    {
        ++$this->copies;
    }

    public function score(): int
    {
        if ($this->score !== null) {
            return $this->score;
        }

        $this->score = 0;

        $matches = $this->matches();

        if ($matches === 0) {
            return $this->score;
        }

        $this->score = 1;

        if ($matches === 1) {
            return $this->score;
        }

        for ($iterator = 0, $match = 1; $iterator < $matches - 1; ++$iterator) {
            $this->score += $match;
            $match *= 2;
        } 

        return $this->score;
    }
}

$scratchcards = [];

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    [
        $card,
        $numberLines,
    ] = explode(': ', $line);

    [
        ,
        $cardNumber,
    ] = preg_split('/ +/', $card);

    [
        $winningNumbers,
        $numbers,
    ] = explode(' | ', $numberLines);

    $scratchcards[$cardNumber] = [
        'card' => new Scratchcard(
            (int) $cardNumber,
            array_map(fn ($item) => (int) $item, array_filter(explode(' ', $numbers))),
            array_map(fn ($item) => (int) $item, array_filter(explode(' ', $winningNumbers))),
        ),
        'copies' => 1,
    ];
}

// dump($scratchcards);

// Determine sum of scratch cards
$sum = 0;
foreach ($scratchcards as $scratchcard) {
    $score = $scratchcard['card']->score();
    $sum += $score;
}

printf('Sum of scratchcard scores: %d' . PHP_EOL, $sum);

// Determine number of scratch cards
for ($cardNumber = 1; $cardNumber <= count($scratchcards); ++$cardNumber) {
    $scratchcard = $scratchcards[$cardNumber];

    for ($copy = 0; $copy < $scratchcard['copies']; ++$copy) {
        $matches = $scratchcard['card']->matches();
        for ($match = 1; $match <= $matches; ++$match) {
            if (! isset($scratchcards[$cardNumber + $match])) {
                continue;
            }

            ++$scratchcards[$cardNumber + $match]['copies'];
        }
    }
}

// Determine sum of scratch cards
$sum = 0;
foreach ($scratchcards as $scratchcard) {
    $sum += $scratchcard['copies'];
}

printf('Total number of scratchcards after copying: %d' . PHP_EOL, $sum);
