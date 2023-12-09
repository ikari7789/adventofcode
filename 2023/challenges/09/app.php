<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/example_input.txt');

class Sequence
{
    public function __construct(
        readonly private array $history,
    ) {
    }

    public function extrapolatePreviousStep(): int
    {
        return $this->extrapolate(array_reverse($this->history));
    }

    public function extrapolateNextStep(): int
    {
        return $this->extrapolate($this->history);
    }

    private function extrapolate(array $history): int
    {
        // Determine step sizes
        $steps = [$history];
        while (count(end($steps)) !== 1 && count(array_unique(end($steps))) !== 1) {
            $steps[] = $this->reduce(end($steps));
        }

        // Add next step at bottom most level
        $lastStep = key($steps);
        $steps[$lastStep][] = end($steps[$lastStep]);

        // Bubble up steps
        for ($step = count($steps) - 2; $step >= 0; --$step) {
            $steps[$step][] = end($steps[$step + 1]) + end($steps[$step]);
        }

        return end($steps[0]);
    }

    private function reduce(array $steps)
    {
        $reduced = [];

        for ($index = 1; $index < count($steps); ++$index) {
            $reduced[] = $steps[$index] - $steps[$index - 1];
        }

        return $reduced;
    }
}

class Sequences implements ArrayAccess, Iterator
{
    private int $index = 0;

    private array $sequences;

    public function sumOfNextSteps(): int
    {
        $sumOfNextSteps = 0;

        foreach ($this->sequences as $sequence) {
            $sumOfNextSteps += $sequence->extrapolateNextStep();
        }

        return $sumOfNextSteps;
    }

    public function sumOfPreviousSteps(): int
    {
        $sumOfNextSteps = 0;

        foreach ($this->sequences as $sequence) {
            $sumOfNextSteps += $sequence->extrapolatePreviousStep();
        }

        return $sumOfNextSteps;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->sequences[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->sequences[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void 
    {
        if (is_null($offset)) {
            $this->sequences[] = $value;

            return;
        }

        $this->sequences[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->sequences[$offset]);
    }

    public function current(): mixed
    {
        return $this->sequences[$this->index];
    }

    public function key(): mixed
    {
        return $this->index;
    }

    public function next(): void
    {
        ++$this->index;
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function valid(): bool
    {
        return $this->offsetExists($this->index);
    }
}

$sequences = new Sequences();

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    $sequences[] = new Sequence(array_map(intval(...), explode(' ', $line)));
}

printf('Sum of next steps: %d' . PHP_EOL, $sequences->sumOfNextSteps());
printf('Sum of next steps: %d' . PHP_EOL, $sequences->sumOfPreviousSteps());
