<?php

namespace Ikari7789\Adventofcode\Year2021\Day18;

use LogicException;

class SnailfishNumber
{
    public function __construct(
        private array $numberPairs
    ) {
        $this->validate($this->numberPairs);
    }

    public function add(SnailfishNumber $snailfishNumber): SnailfishNumber
    {
        return $this;
    }

    public function walk(array &$pairs, array $stack = [], int $depth = 0)
    {
        $left  = $pairs[0];
        $right = $pairs[1];

        $explodeChild = false;
        if ($depth === 3) {
            $explodeChild = true;
        }

        if (is_array($left)) {
            $this->walk($left, $stack, $depth + 1);
        }

        if (is_array($right)) {
            $this->walk($right, $stack, $depth + 1);
        }

        printf('Depth: %d' . PHP_EOL, $depth);
        printf('Stack: %s' . PHP_EOL, implode(',', $stack));
    }

    public function reduce(): array
    {
        $numberPairs = $this->numberPairs;

        $this->walk($numberPairs);

        // foreach ($numberPairs as $pair1) {
        //     if (is_array($pair1)) {
        //         foreach ($pair1 as $pair2) {
        //             if (is_array($pair2)) {
        //                 foreach ($pair2 as $pair3) {
        //                     if (is_array($pair3)) {
        //                         foreach ($pair3 as $pair4) {
        //                             if (! is_array($pair4)) {
        //                                 printf('Pair 4: %d' . PHP_EOL, $pair4);
        //                             }
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }

        return $numberPairs;
    }

    public function __toString(): string
    {
        return $this->toString($this->numberPairs);
    }

    private function validate(array $numberPairs): void
    {
        if (count($numberPairs) !== 2) {
            throw new LogicException('Only pairs allowed');
        }

        foreach ($numberPairs as $pair) {
            if (is_array($pair)) {
                $this->validate($pair);
            } elseif (! is_int($pair)) {
                throw new LogicException('Only integers allowed');
            }
        }
    }

    private function toString(array $numberPairs): string
    {
        $string = '[';

        if (is_array($numberPairs[0])) {
            $string .= $this->toString($numberPairs[0]) . ',';
        } elseif (is_int($numberPairs[0])) {
            $string .= $numberPairs[0] . ',';
        }

        if (is_array($numberPairs[1])) {
            $string .= $this->toString($numberPairs[1]);
        } elseif (is_int($numberPairs[1])) {
            $string .= $numberPairs[1];
        }

        $string .= ']';

        return $string;
    }
}