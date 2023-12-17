<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

enum Direction: string
{
    case North = 'North';
    case East = 'East';
    case South = 'South';
    case West = 'West';
}

enum Tile: string
{
    case Lit = '#';
    case North = '^';
    case East = '>';
    case South = 'v';
    case West = '<';
    case EmptySpace = '.';
    case RightAngleMirror = '/';
    case LeftAngleMirror = '\\';
    case VerticalSplitter = '|';
    case HorizontalSplitter = '-';
}

class Tiles implements Stringable
{
    private array $tiles;

    private array $litTiles = [];

    private array $directionalTiles = [];

    public function addRow(array $row): self
    {
        $this->tiles[] = $row;

        return $this;
    }

    public function maximumLitTilesCount(): int
    {
        $maxCount = 0;

        $minX = 0;
        $maxX = count($this->tiles[0]) - 1;
        $minY = 0;
        $maxY = count($this->tiles) - 1;

        for ($x = $minX; $x < count($this->tiles[0]); ++$x) {
            $maxCount = max($maxCount, $this->litTilesCount($x, $minY, Direction::South));
            $maxCount = max($maxCount, $this->litTilesCount($x, $maxY, Direction::North));
        }

        for ($y = $minY; $y <= $maxY; ++$y) {
            $maxCount = max($maxCount, $this->litTilesCount($minX, $y, Direction::East));
            $maxCount = max($maxCount, $this->litTilesCount($maxX, $y, Direction::West));
        }

        return $maxCount;
    }

    public function litTilesCount(int $x = 0, int $y = 0, Direction $direction = Direction::East): int
    {
        $litTiles = 0;

        foreach ($this->lightTiles($x, $y, $direction) as $row) {
            foreach ($row as $column) {
                if ($column === Tile::Lit) {
                    ++$litTiles;
                }
            }
        }

        return $litTiles;
    }

    public function lightTiles(int $x = 0, int $y = 0, Direction $direction = Direction::East): array
    {
        $this->litTiles = array_fill(0, count($this->tiles), array_fill(0, count($this->tiles[0]), Tile::EmptySpace));
        $this->directionalTiles = $this->tiles;

        $this->lightTileFrom($x, $y, $direction);

        return $this->litTiles;
    }

    public function lightTileFrom(int $x, int $y, Direction $direction, array &$history = []): void
    {
        while (isset($this->tiles[$y][$x])) {
            $key = "{$x}:{$y}:{$direction->value}";

            if (in_array($key, $history, strict: true)) {
                return;
            }

            $history[] = $key;

            $this->litTiles[$y][$x] = Tile::Lit;
            // $this->directionalTiles[$y][$x] = match ($direction) {
            //     Direction::North => Tile::North,
            //     Direction::East =>Tile::East,
            //     Direction::South => Tile::South,
            //     Direction::West => Tile::West,
            // };

            if ($this->tiles[$y][$x] === Tile::EmptySpace) {
                switch ($direction) {
                    case Direction::North:
                        $this->directionalTiles[$y][$x] = Tile::North;
                        $x = $x;
                        $y = $y - 1;

                        break;
                    case Direction::East:
                        $this->directionalTiles[$y][$x] = Tile::East;
                        $x = $x + 1;
                        $y = $y;

                        break;
                    case Direction::South:
                        $this->directionalTiles[$y][$x] = Tile::South;
                        $x = $x;
                        $y = $y + 1;

                        break;
                    case Direction::West:
                        $this->directionalTiles[$y][$x] = Tile::West;
                        $x = $x - 1;
                        $y = $y;

                        break;
                }
            } elseif ($this->tiles[$y][$x] === Tile::RightAngleMirror) {
                switch ($direction) {
                    case Direction::North:
                        $direction = Direction::East;
                        $x = $x + 1;
                        $y = $y;

                        break;
                    case Direction::East:
                        $direction = Direction::North;
                        $x = $x;
                        $y = $y - 1;

                        break;
                    case Direction::South:
                        $direction = Direction::West;
                        $x = $x - 1;
                        $y = $y;

                        break;
                    case Direction::West:
                        $direction = Direction::South;
                        $x = $x;
                        $y = $y + 1;

                        break;
                }
            } elseif ($this->tiles[$y][$x] === Tile::LeftAngleMirror) {
                switch ($direction) {
                    case Direction::North:
                        $direction = Direction::West;
                        $x = $x - 1;
                        $y = $y;

                        break;
                    case Direction::East:
                        $direction = Direction::South;
                        $x = $x;
                        $y = $y + 1;

                        break;
                    case Direction::South:
                        $direction = Direction::East;
                        $x = $x + 1;
                        $y = $y;

                        break;
                    case Direction::West:
                        $direction = Direction::North;
                        $x = $x;
                        $y = $y - 1;

                        break;
                }
            } elseif ($this->tiles[$y][$x] === Tile::VerticalSplitter) {
                switch ($direction) {
                    case Direction::North:
                        $x = $x;
                        $y = $y - 1;

                        break;
                    case Direction::East:
                        $direction = Direction::South;

                        // Start lighting tiles in the opposite direction
                        $this->lightTileFrom($x, $y - 1, Direction::North, $history);

                        $x = $x;
                        $y = $y + 1;

                        break;
                    case Direction::South:
                        $x = $x;
                        $y = $y + 1;

                        break;
                    case Direction::West:
                        $direction = Direction::South;

                        // Start lighting tiles in the opposite direction
                        $this->lightTileFrom($x, $y - 1, Direction::North, $history);

                        $x = $x;
                        $y = $y + 1;

                        break;
                }
            } elseif ($this->tiles[$y][$x] === Tile::HorizontalSplitter) {
                switch ($direction) {
                    case Direction::North:
                        $direction = Direction::East;

                        // Start lighting tiles in the opposite direction
                        $this->lightTileFrom($x - 1, $y, Direction::West, $history);

                        $x = $x + 1;
                        $y = $y;

                        break;
                    case Direction::East:
                        $x = $x + 1;
                        $y = $y;

                        break;
                    case Direction::South:
                        $direction = Direction::East;

                        // Start lighting tiles in the opposite direction
                        $this->lightTileFrom($x - 1, $y, Direction::West, $history);

                        $x = $x + 1;
                        $y = $y;

                        break;
                    case Direction::West:
                        $x = $x - 1;
                        $y = $y;

                        break;
                }
            }

            // echo $this . PHP_EOL;

            // readline();
        }
    }

    public function __toString(): string
    {
        $string = '';

        for ($y = 0; $y < count($this->tiles); ++$y) {
            for ($x = 0; $x < count($this->tiles[$y]); ++$x) {
                $string .= $this->tiles[$y][$x]->value;
            }

            $string .= '    ';

            for ($x = 0; $x < count($this->tiles[$y]); ++$x) {
                $string .= $this->directionalTiles[$y][$x]->value;
            }

            $string .= '    ';

            for ($x = 0; $x < count($this->tiles[$y]); ++$x) {
                $string .= $this->litTiles[$y][$x]->value;
            }

            $string .= PHP_EOL;
        }

        return $string;
    }
}

$tiles = new Tiles();

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    $tiles->addRow(array_map(fn($tile) => Tile::from($tile), str_split($line)));
}

printf('Number of lit tiles: %d' . PHP_EOL, $tiles->litTilesCount());
printf('Maximum number of tiles that can be lit: %d' . PHP_EOL, $tiles->maximumLitTilesCount());
