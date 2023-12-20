<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

enum Direction: string
{
    case Up = 'U';
    case Down = 'D';
    case Left = 'L';
    case Right = 'R';
}

class DigPlan
{
    public function __construct(
        readonly private Direction $direction,
        readonly private int $distance,
        readonly private string $rgb,
    ) {}

    public function direction(): Direction
    {
        return $this->direction;
    }

    public function distance(): int
    {
        return $this->distance;
    }

    public function rgb(): array
    {
        return [
            'red' => hexdec(substr($this->rgb, 0, 2)),
            'green' => hexdec(substr($this->rgb, 2, 2)),
            'blue' => hexdec(substr($this->rgb, 4, 2)),
        ];
    }

    public function trueDirection(): Direction
    {
        $hex = substr($this->rgb, 5);
        $dec = hexdec($hex);

        return match ($dec) {
            0 => Direction::Right,
            1 => Direction::Down,
            2 => Direction::Left,
            3 => Direction::Up,
        };
    }

    public function trueDistance(): int
    {
        $hex = substr($this->rgb, 0, 5);

        return hexdec($hex);
    }
}

class Lagoon implements Stringable
{
    private array $digPlans = [];

    private array $trueDigPlan = [];

    private array $currentPosition = [0, 0];

    private array $trueCurrentPosition = [0, 0];

    private array $overhead = [];

    private array $filled = [];

    private array $path = [[0,0]];

    private array $truePath = [[0,0]];

    private int $perimeter = 0;

    private int $truePerimeter = 0;

    public function addDigPlan(DigPlan $digPlan): void
    {
        $this->digPlans[] = $digPlan;

        switch ($digPlan->direction()) {
            case Direction::Up:
                $from = $this->currentPosition[1];
                $to = $this->currentPosition[1] - $digPlan->distance();

                for ($y = $from; $y >= $to; --$y) {
                    if (!isset($this->overhead[$y])) {
                        $this->overhead[$y] = [];
                    }

                    $this->overhead[$y][$this->currentPosition[0]] = $this->rgbToTerminal($digPlan->rgb(), '#');

                    ++$this->perimeter;
                    $this->currentPosition = [$this->currentPosition[0], $y];
                }

                break;

            case Direction::Right:
                if (!isset($this->overhead[$this->currentPosition[1]])) {
                    $this->overhead[$this->currentPosition[1]] = [];
                }

                $from = $this->currentPosition[0];
                $to = $this->currentPosition[0] + $digPlan->distance();

                for ($x = $from; $x <= $to; ++$x) {
                    $this->overhead[$this->currentPosition[1]][$x] = $this->rgbToTerminal($digPlan->rgb(), '#');

                    ++$this->perimeter;
                    $this->currentPosition = [$x, $this->currentPosition[1]];
                }

                break;

            case Direction::Down:
                $from = $this->currentPosition[1];
                $to = $this->currentPosition[1] + $digPlan->distance();

                for ($y = $from; $y <= $to; ++$y) {
                    if (!isset($this->overhead[$y])) {
                        $this->overhead[$y] = [];
                    }

                    $this->overhead[$y][$this->currentPosition[0]] = $this->rgbToTerminal($digPlan->rgb(), '#');

                    ++$this->perimeter;
                    $this->currentPosition = [$this->currentPosition[0], $y];
                }

                break;

            case Direction::Left:
                if (!isset($this->overhead[$this->currentPosition[1]])) {
                    $this->overhead[$this->currentPosition[1]] = [];
                }

                $from = $this->currentPosition[0];
                $to = $this->currentPosition[0] - $digPlan->distance();

                for ($x = $from; $x >= $to; --$x) {
                    $this->overhead[$this->currentPosition[1]][$x] = $this->rgbToTerminal($digPlan->rgb(), '#');

                    ++$this->perimeter;
                    $this->currentPosition = [$x, $this->currentPosition[1]];
                }

                break;
        }

        --$this->perimeter;
        $this->path[] = $this->currentPosition;

        // Fill in empty slots and arrange keys
        ksort($this->overhead);
        $minY = array_key_first($this->overhead);
        $maxY = array_key_last($this->overhead);
        $minX = 0;
        $maxX = 0;
        foreach ($this->overhead as &$row) {
            ksort($row);
            $minX = min($minX, array_key_first($row));
            $maxX = max($maxX, array_key_last($row));
        }
        unset($row);

        for ($y = $minY; $y <= $maxY; ++$y) {
            for ($x = $minX; $x <= $maxX; ++$x) {
                if (isset($this->overhead[$y][$x])) {
                    continue;
                }

                $this->overhead[$y][$x] = ' ';
            }
        }

        switch ($digPlan->trueDirection()) {
            case Direction::Up:
                $from = $this->trueCurrentPosition[1];
                $to = $this->trueCurrentPosition[1] - $digPlan->trueDistance();

                for ($y = $from; $y >= $to; --$y) {
                    ++$this->truePerimeter;
                    $this->trueCurrentPosition = [$this->trueCurrentPosition[0], $y];
                }

                break;

            case Direction::Right:
                $from = $this->trueCurrentPosition[0];
                $to = $this->trueCurrentPosition[0] + $digPlan->trueDistance();

                for ($x = $from; $x <= $to; ++$x) {
                    ++$this->truePerimeter;
                    $this->trueCurrentPosition = [$x, $this->trueCurrentPosition[1]];
                }

                break;

            case Direction::Down:
                $from = $this->trueCurrentPosition[1];
                $to = $this->trueCurrentPosition[1] + $digPlan->trueDistance();

                for ($y = $from; $y <= $to; ++$y) {
                    ++$this->truePerimeter;
                    $this->trueCurrentPosition = [$this->trueCurrentPosition[0], $y];
                }

                break;

            case Direction::Left:
                $from = $this->trueCurrentPosition[0];
                $to = $this->trueCurrentPosition[0] - $digPlan->trueDistance();

                for ($x = $from; $x >= $to; --$x) {
                    ++$this->truePerimeter;
                    $this->trueCurrentPosition = [$x, $this->trueCurrentPosition[1]];
                }

                break;
        }

        --$this->truePerimeter;
        $this->truePath[] = $this->trueCurrentPosition;
    }

    public function area(): int
    {
        $xCoordinates = array_map(fn($coordinate) => $coordinate[0], $this->path);
        $yCoordinates = array_map(fn($coordinate) => $coordinate[1], $this->path);

        $leftSum           = 0;
        $xCoordinatesCount = count($xCoordinates);
        for ($index = 0; $index < $xCoordinatesCount; ++$index) {
            $yIndex = $index + 1;
            if ($yIndex === $xCoordinatesCount) {
                $yIndex = 0;
            }

            $leftSum += $xCoordinates[$index] * $yCoordinates[$yIndex];
        }

        $rightSum          = 0;
        $yCoordinatesCount = count($yCoordinates);
        for ($index = 0; $index < $yCoordinatesCount; ++$index) {
            $xIndex = $index + 1;
            if ($xIndex === $yCoordinatesCount) {
                $xIndex = 0;
            }

            $rightSum += $yCoordinates[$index] * $xCoordinates[$xIndex];
        }

        $area = abs($leftSum - $rightSum) / 2;

        return $area + ($this->perimeter / 2) + 1;
    }

    public function trueArea(): float
    {
        $xCoordinates = array_map(fn($coordinate) => $coordinate[0], $this->truePath);
        $yCoordinates = array_map(fn($coordinate) => $coordinate[1], $this->truePath);

        $leftSum           = 0;
        $xCoordinatesCount = count($xCoordinates);
        for ($index = 0; $index < $xCoordinatesCount; ++$index) {
            $yIndex = $index + 1;
            if ($yIndex === $xCoordinatesCount) {
                $yIndex = 0;
            }

            $leftSum += $xCoordinates[$index] * $yCoordinates[$yIndex];
        }

        $rightSum          = 0;
        $yCoordinatesCount = count($yCoordinates);
        for ($index = 0; $index < $yCoordinatesCount; ++$index) {
            $xIndex = $index + 1;
            if ($xIndex === $yCoordinatesCount) {
                $xIndex = 0;
            }

            $rightSum += $yCoordinates[$index] * $xCoordinates[$xIndex];
        }

        $area = abs($leftSum - $rightSum) / 2;

        return $area + ($this->truePerimeter / 2) + 1;
    }

    private function rgbToTerminal(array $rgb, string $text): string
    {
        // \e[38;2;⟨r⟩;⟨g⟩;⟨b⟩m for RGB foreground color
        // \e[48;2;⟨r⟩;⟨g⟩;⟨b⟩m for RGB background color
        return "\e[38;2;{$rgb['red']};{$rgb['green']};{$rgb['blue']}m{$text}\e[m";
    }

    public function printOverhead(): string
    {
        $string = '';

        $viewToShow = $this->overhead;

        foreach ($viewToShow as $row) {
            foreach ($row as $column) {
                $string .= $column;
            }
            $string .= PHP_EOL;
        }

        return $string;
        for ($y = array_key_first($viewToShow); $y < array_key_last($viewToShow); ++$y) {
            for ($x = array_key_first($viewToShow[$y]); $y < array_key_last($viewToShow[$y]); ++$y) {
                $string .= $viewToShow[$y][$x];
            }
            $string .= PHP_EOL;
        }

        return $string;
    }

    public function printFilled(): string
    {
        $string = '';

        $viewToShow = $this->filled;

        foreach ($viewToShow as $row) {
            foreach ($row as $column) {
                $string .= $column;
            }
            $string .= PHP_EOL;
        }

        return $string;
        for ($y = array_key_first($viewToShow); $y < array_key_last($viewToShow); ++$y) {
            for ($x = array_key_first($viewToShow[$y]); $y < array_key_last($viewToShow[$y]); ++$y) {
                $string .= $viewToShow[$y][$x];
            }
            $string .= PHP_EOL;
        }

        return $string;
    }

    public function __toString(): string
    {
        $string = '';

        $viewToShow = $this->filled;

        foreach ($viewToShow as $row) {
            foreach ($row as $column) {
                $string .= $column;
            }
            $string .= PHP_EOL;
        }

        return $string;
        for ($y = array_key_first($viewToShow); $y < array_key_last($viewToShow); ++$y) {
            for ($x = array_key_first($viewToShow[$y]); $y < array_key_last($viewToShow[$y]); ++$y) {
                $string .= $viewToShow[$y][$x];
            }
            $string .= PHP_EOL;
        }

        return $string;
    }
}

$lagoon = new Lagoon();

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    [
        $direction,
        $distance,
        $rgb
    ] = explode(' ', $line);

    $lagoon->addDigPlan(new DigPlan(Direction::from($direction), (int) $distance, substr($rgb, 2, 6)));
}

// echo $lagoon->printOverhead() . PHP_EOL;

// echo $lagoon->printFilled() . PHP_EOL;

printf('Cubic meters of lava: %d' . PHP_EOL, $lagoon->area());

printf('True cubic meters of lava: %d' . PHP_EOL, $lagoon->trueArea());
