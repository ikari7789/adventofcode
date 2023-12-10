<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

enum Direction: string
{
    case North = 'north';
    case East  = 'east';
    case South = 'south';
    case West  = 'west';
}

enum Marker: string
{
    case NorthSouthPipe = '|';
    case EastWestPipe   = '-';
    case NorthEastPipe  = 'L';
    case NorthWestPipe  = 'J';
    case SouthWestPipe  = '7';
    case SouthEastPipe  = 'F';
    case Ground         = '.';
    case Start          = 'S';

    public function isStart(): bool
    {
        return $this === Marker::Start;
    }

    public function isPipe(): bool
    {
        return $this !== Marker::Ground;
    }

    public function canConnect(Marker $marker, Direction $direction): bool
    {
        $canConnect = false;

        switch ($this) {
            case Marker::NorthSouthPipe:
                $canConnect = match ($direction) {
                    Direction::North => in_array($marker, [Marker::Start, Marker::NorthSouthPipe, Marker::SouthEastPipe, Marker::SouthWestPipe], strict: true),
                    Direction::South => in_array($marker, [Marker::Start, Marker::NorthSouthPipe, Marker::NorthEastPipe, Marker::NorthWestPipe], strict: true),
                    default => false,
                };

                break;

            case Marker::EastWestPipe:
                $canConnect = match ($direction) {
                    Direction::East => in_array($marker, [Marker::Start, Marker::EastWestPipe, Marker::NortWestPipe, Marker::SouthWestPipe], strict: true),
                    Direction::West => in_array($marker, [Marker::Start, Marker::EastWestPipe, Marker::NorthEastPipe, Marker::SouthEastPipe], strict: true),
                    default => false,
                };

                break;

            case Marker::NorthEastPipe:
                $canConnect = match ($direction) {
                    Direction::North => in_array($marker, [Marker::Start, Marker::Start, Marker::NorthSouthPipe, Marker::SouthWestPipe, Marker::SouthEastPipe], strict: true),
                    Direction::East  => in_array($marker, [Marker::Start, Marker::EastWestPipe, Marker::NorthWestPipe, Marker::SouthWestPipe], strict: true),
                    default => false,
                };

                break;

            case Marker::NorthWestPipe:
                $canConnect = match ($direction) {
                    Direction::North => in_array($marker, [Marker::Start, Marker::NorthSouthPipe, Marker::SouthWestPipe, Marker::SouthEastPipe], strict: true),
                    Direction::West  => in_array($marker, [Marker::Start, Marker::EastWestPipe, Marker::NorthEastPipe, Marker::SouthEastPipe], strict: true),
                    default => false,
                };

                break;

            case Marker::SouthWestPipe:
                $canConnect = match ($direction) {
                    Direction::South => in_array($marker, [Marker::Start, Marker::NorthSouthPipe, Marker::NorthEastPipe, Marker::NorthWestPipe], strict: true),
                    Direction::West  => in_array($marker, [Marker::Start, Marker::EastWestPipe, Marker::NorthEastPipe, Marker::SouthEastPipe], strict: true),
                    default => false,
                };

                break;

            case Marker::SouthEastPipe:
                $canConnect = match ($direction) {
                    Direction::South => in_array($marker, [Marker::Start, Marker::NorthSouthPipe, Marker::NorthEastPipe, Marker::NorthWestPipe], strict: true),
                    Direction::East  => in_array($marker, [Marker::Start, Marker::EastWestPipe, Marker::NorthWestPipe, Marker::SouthWestPipe], strict: true),

                    default => false,
                };

                break;

            case Marker::Start:
                $canConnect = match ($direction) {
                    Direction::North => in_array($marker, [Marker::NorthSouthPipe, Marker::SouthEastPipe, Marker::SouthWestPipe], strict: true),
                    Direction::South => in_array($marker, [Marker::NorthSouthPipe, Marker::NorthEastPipe, Marker::NorthWestPipe], strict: true),
                    Direction::East  => in_array($marker, [Marker::EastWestPipe, Marker::NortWestPipe, Marker::SouthWestPipe], strict: true),
                    Direction::West  => in_array($marker, [Marker::EastWestPipe, Marker::NorthEastPipe, Marker::SouthEastPipe], strict: true),
                    default => false,
                };

                break;
        }

        return $canConnect;
    }
}

class Coordinate implements Stringable
{
    private ?Coordinate $north = null;
    private ?Coordinate $east  = null;
    private ?Coordinate $south = null;
    private ?Coordinate $west  = null;

    public function __construct(
        readonly private int $x,
        readonly private int $y,
        readonly private Marker $marker,
    ) {}

    public function x(): int
    {
        return $this->x;
    }

    public function y(): int
    {
        return $this->y;
    }

    public function marker(): Marker
    {
        return $this->marker;
    }

    public function north(): ?Coordinate
    {
        return $this->north;
    }

    public function east(): ?Coordinate
    {
        return $this->east;
    }

    public function south(): ?Coordinate
    {
        return $this->south;
    }

    public function west(): ?Coordinate
    {
        return $this->west;
    }

    public function setNorth(Coordinate $north): self
    {
        $this->north = $north;

        return $this;
    }

    public function setEast(Coordinate $east): self
    {
        $this->east = $east;

        return $this;
    }

    public function setSouth(Coordinate $south): self
    {
        $this->south = $south;

        return $this;
    }

    public function setWest(Coordinate $west): self
    {
        $this->west = $west;

        return $this;
    }

    public function __toString(): string
    {
        if (
            $this->north === null
            && $this->east === null
            && $this->south === null
            && $this->west === null
        ) {
            return ' ';
        }

        return match ($this->marker) {
            Marker::NorthSouthPipe => '│',
            Marker::EastWestPipe   => '─',
            Marker::NorthEastPipe  => '└',
            Marker::NorthWestPipe  => '┘',
            Marker::SouthWestPipe  => '┐',
            Marker::SouthEastPipe  => '┌',
            Marker::Ground         => ' ',
            Marker::Start          => 'S',
        };
    }
}

class Map implements Stringable
{
    private array $grid = [];

    private ?Coordinate $start = null;

    private array $path = [];

    public function addCoordinate(int $x, int $y, string $marker)
    {
        // Add new row if not there yet
        if (!isset($this->grid[$y])) {
            $this->grid[$y] = [];
        }

        // Add coordinate to grid
        $this->grid[$y][$x] = new Coordinate($x, $y, Marker::from($marker));

        if ($this->grid[$y][$x]->marker()->isStart()) {
            $this->start = $this->grid[$y][$x];
        }

        // If this isn't a pipe
        if (!$this->grid[$y][$x]->marker()->isPipe()) {
            return;
        }

        $connections = [];

        if ($this->grid[$y][$x]->marker() === Marker::NorthSouthPipe) {
            $connections[] = ['direction' => Direction::North, 'opposing_direction' => Direction::South, 'x' => $x, 'y' => $y - 1];
            $connections[] = ['direction' => Direction::South, 'opposing_direction' => Direction::North, 'x' => $x, 'y' => $y + 1];
        } elseif ($this->grid[$y][$x]->marker() === Marker::EastWestPipe) {
            $connections[] = ['direction' => Direction::East, 'opposing_direction' => Direction::West, 'x' => $x + 1, 'y' => $y];
            $connections[] = ['direction' => Direction::West, 'opposing_direction' => Direction::East, 'x' => $x - 1, 'y' => $y];
        } elseif ($this->grid[$y][$x]->marker() === Marker::NorthEastPipe) {
            $connections[] = ['direction' => Direction::North, 'opposing_direction' => Direction::South, 'x' => $x, 'y' => $y - 1];
            $connections[] = ['direction' => Direction::East, 'opposing_direction' => Direction::West, 'x' => $x + 1, 'y' => $y];
        } elseif ($this->grid[$y][$x]->marker() === Marker::NorthWestPipe) {
            $connections[] = ['direction' => Direction::North, 'opposing_direction' => Direction::South, 'x' => $x, 'y' => $y - 1];
            $connections[] = ['direction' => Direction::West, 'opposing_direction' => Direction::East, 'x' => $x - 1, 'y' => $y];
        } elseif ($this->grid[$y][$x]->marker() === Marker::SouthEastPipe) {
            $connections[] = ['direction' => Direction::South, 'opposing_direction' => Direction::North, 'x' => $x, 'y' => $y + 1];
            $connections[] = ['direction' => Direction::East, 'opposing_direction' => Direction::West, 'x' => $x + 1, 'y' => $y];
        } elseif ($this->grid[$y][$x]->marker() === Marker::SouthWestPipe) {
            $connections[] = ['direction' => Direction::South, 'opposing_direction' => Direction::North, 'x' => $x, 'y' => $y + 1];
            $connections[] = ['direction' => Direction::West, 'opposing_direction' => Direction::East, 'x' => $x - 1, 'y' => $y];
        } elseif ($this->grid[$y][$x]->marker() === Marker::Start) {
            $connections[] = ['direction' => Direction::North, 'opposing_direction' => Direction::South, 'x' => $x, 'y' => $y - 1];
            $connections[] = ['direction' => Direction::South, 'opposing_direction' => Direction::North, 'x' => $x, 'y' => $y + 1];
            $connections[] = ['direction' => Direction::East, 'opposing_direction' => Direction::West, 'x' => $x + 1, 'y' => $y];
            $connections[] = ['direction' => Direction::West, 'opposing_direction' => Direction::East, 'x' => $x - 1, 'y' => $y];
        }

        foreach ($connections as $connection) {
            if (!isset($this->grid[$connection['y']][$connection['x']])) {
                continue;
            }

            if (!$this->grid[$y][$x]->marker()->canConnect($this->grid[$connection['y']][$connection['x']]->marker(), $connection['direction'])) {
                continue;
            }

            $this->grid[$y][$x]->{'set' . ucfirst($connection['direction']->value)}($this->grid[$connection['y']][$connection['x']]);
            $this->grid[$connection['y']][$connection['x']]->{'set' . ucfirst($connection['opposing_direction']->value)}($this->grid[$y][$x]);
        }
    }

    public function farthestDistanceFromStart(): int
    {
        if ($this->start === null) {
            throw new Exception('Start not yet charted.');
        }

        // Determine start location
        $startX = $this->start->x();
        $startY = $this->start->y();

        $lastDirection = null;
        $next          = $this->start;

        $path = [$next];
        do {
            // Get first available direction for moving
            if ($lastDirection !== Direction::North && $next->north() !== null) {
                $lastDirection = Direction::South;
                $next          = $next->north();
            } elseif ($lastDirection !== Direction::East && $next->east() !== null) {
                $lastDirection = Direction::West;
                $next          = $next->east();
            } elseif ($lastDirection !== Direction::South && $next->south() !== null) {
                $lastDirection = Direction::North;
                $next          = $next->south();
            } elseif ($lastDirection !== Direction::West && $next->west() !== null) {
                $lastDirection = Direction::East;
                $next          = $next->west();
            }

            $x = $next->x();
            $y = $next->y();

            if ([$x, $y] !== [$startX, $startY]) {
                $path[] = $next;
            }
        } while ([$x, $y] !== [$startX, $startY]);

        $this->path = $path;

        // If the path has an odd number of stops, round up as there will be one further item
        return (int) ceil(count($path) / 2);
    }

    public function internalPointsInPath(): int
    {
        if (empty($this->path)) {
            throw new Exception('Calculate farthest distance from start first.');
        }

        $xCoordinates = array_map(fn($coordinate) => $coordinate->x(), $this->path);
        $yCoordinates = array_map(fn($coordinate) => $coordinate->y(), $this->path);

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

        return $area - count($this->path) / 2 + 1;
    }

    public function __toString(): string
    {
        $string = '';

        $pathAsCoords = array_map(fn($coordinate) => $coordinate->x() . '_' . $coordinate->y(), $this->path);

        $yCount = count($this->grid);
        for ($y = 0; $y < $yCount; ++$y) {
            $xCount = count($this->grid[$y]);
            for ($x = 0; $x < $xCount; ++$x) {
                if (!in_array($x . '_' . $y, $pathAsCoords, strict: true)) {
                    $string .= '.';

                    continue;
                }

                $string .= $this->grid[$y][$x];
            }
            $string .= PHP_EOL;
        }

        return $string;
    }
}

$map = new Map();

$y = 0;
$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    $coordinates = str_split($line);

    foreach ($coordinates as $x => $marker) {
        $map->addCoordinate($x, $y, $marker);
    }

    ++$y;
}
unset($y);

printf('Farthest distance from start: %d' . PHP_EOL, $map->farthestDistanceFromStart()); // should be 6909 for input.txt
printf('Internal point count on path: %d' . PHP_EOL, $map->internalPointsInPath()); // should be 461 for input.txt
