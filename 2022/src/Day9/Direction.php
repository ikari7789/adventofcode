<?php

declare(strict_types=1);

namespace Ikari7789\Adventofcode\Year2022\Day9;

enum Direction: string
{
    case Up = 'U';
    case Down = 'D';
    case Left = 'L';
    case Right = 'R';

    public static function fromString(string $direction): static
    {
        return match (true) {
            $direction === static::Up->value => static::Up,
            $direction === static::Down->value => static::Down,
            $direction === static::Left->value => static::Left,
            $direction === static::Right->value => static::Right,
        };
    }
}
