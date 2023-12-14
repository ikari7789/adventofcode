<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');
define('CYCLES', 1000000000);

enum Direction: string
{
    case North = 'North';
    case East = 'East';
    case South = 'South';
    case West = 'West';
}

enum PlatformObject: string
{
    case RoundedRock = 'O';
    case CubeShapedRock = '#';
    case EmptySpace = '.';
}

class Platform implements Stringable
{
    private array $platform;

    public function addLine(string $line): void
    {
        $line = str_split($line);

        foreach ($line as &$char) {
            $char = PlatformObject::from($char);
        }

        $this->platform[] = $line;
    }

    public function totalLoad(): int
    {
        $total = 0;

        $platform = array_reverse($this->platform);
        for ($y = 0; $y < count($platform); ++$y) {
            for ($x = 0; $x < count($platform[$y]); ++$x) {
                if ($platform[$y][$x] === PlatformObject::RoundedRock) {
                    $total += $y + 1;
                }
            }
        }

        return $total;
    }

    public function spinCycle(): array
    {
        $this->platform = $this->tiltPlatform(Direction::North); // echo $this . PHP_EOL;
        $this->platform = $this->tiltPlatform(Direction::West); // echo $this . PHP_EOL;
        $this->platform = $this->tiltPlatform(Direction::South); // echo $this . PHP_EOL;
        $this->platform = $this->tiltPlatform(Direction::East); // echo $this . PHP_EOL;

        return $this->platform;
    }

    public function tiltPlatform(Direction $direction): array
    {
        return $this->{'tiltPlatform' . ucfirst($direction->value) }($this->platform);
        ;
    }

    private function tiltPlatformNorth(array $platform): array
    {
        do {
            $previousPass = $platform;
            for ($y = 1; $y < count($platform); ++$y) {
                for ($x = 0; $x < count($platform[$y]); ++$x) {
                    if (
                        $platform[$y][$x] === PlatformObject::RoundedRock
                        && $platform[$y - 1][$x] === PlatformObject::EmptySpace
                    ) {
                        $platform[$y - 1][$x] = PlatformObject::RoundedRock;
                        $platform[$y][$x] = PlatformObject::EmptySpace;
                    }
                }
            }
        } while ($previousPass !== $platform);

        return $platform;
    }

    private function tiltPlatformEast(array $platform): array
    {
        do {
            $previousPass = $platform;
            for ($y = 0; $y < count($platform); ++$y) {
                for ($x = 0; $x < count($platform[$y]) - 1; ++$x) {
                    if (
                        $platform[$y][$x] === PlatformObject::RoundedRock
                        && $platform[$y][$x + 1] === PlatformObject::EmptySpace
                    ) {
                        $platform[$y][$x + 1] = PlatformObject::RoundedRock;
                        $platform[$y][$x] = PlatformObject::EmptySpace;
                    }
                }
            }
        } while ($previousPass !== $platform);

        return $platform;
    }

    private function tiltPlatformSouth(array $platform): array
    {
        do {
            $previousPass = $platform;
            for ($y = 0; $y < count($platform) - 1; ++$y) {
                for ($x = 0; $x < count($platform[$y]); ++$x) {
                    if (
                        $platform[$y][$x] === PlatformObject::RoundedRock
                        && $platform[$y + 1][$x] === PlatformObject::EmptySpace
                    ) {
                        $platform[$y + 1][$x] = PlatformObject::RoundedRock;
                        $platform[$y][$x] = PlatformObject::EmptySpace;
                    }
                }
            }
        } while ($previousPass !== $platform);

        return $platform;
    }

    private function tiltPlatformWest(array $platform): array
    {
        do {
            $previousPass = $platform;
            for ($y = 0; $y < count($platform); ++$y) {
                for ($x = 1; $x < count($platform[$y]); ++$x) {
                    if (
                        $platform[$y][$x] === PlatformObject::RoundedRock
                        && $platform[$y][$x - 1] === PlatformObject::EmptySpace
                    ) {
                        $platform[$y][$x - 1] = PlatformObject::RoundedRock;
                        $platform[$y][$x] = PlatformObject::EmptySpace;
                    }
                }
            }
        } while ($previousPass !== $platform);

        return $platform;
    }

    public function __toString(): string
    {
        $string = '';

        foreach ($this->platform as $row) {
            foreach ($row as $column) {
                $string .= $column->value;
            }
            $string .= PHP_EOL;
        }

        return $string;
    }
}

$platform = new Platform();

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);
    $platform->addLine($line);
}

// echo $platform . PHP_EOL;
$platform->tiltPlatform(Direction::North);
// echo $platform . PHP_EOL;

printf('Total load on north support beams: %d' . PHP_EOL . PHP_EOL, $platform->totalLoad());

$platform = new Platform();

$input = new SplFileObject(INPUT_FILE);
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);
    $platform->addLine($line);
}

// echo $platform . PHP_EOL;

$cycles = CYCLES;
for ($cycle = 0; $cycle < $cycles; ++$cycle) {
    // printf('After %d cycles:' . PHP_EOL, $cycle + 1);
    $platform->spinCycle();

    $loads[] = $platform->totalLoad();

    if (($cycle + 1) % 10 === 0) {
        dump($loads);
        // $values = array_count_values($loads);
        // printf('Mode after %d cycles: %d' . PHP_EOL, $cycle, array_search(max($values), $values));
        // printf('Total load on north support beams after %d cycles: %d' . PHP_EOL . PHP_EOL, $cycle + 1, $platform->totalLoad());
    }
}

/**
^ array:430 [
  0 => 100593
  1 => 100318
  2 => 100211
  3 => 100017
  4 => 99841
  5 => 99759
  6 => 99648
  7 => 99611
  8 => 99569
  9 => 99528
  10 => 99432
  11 => 99402
  12 => 99421
  13 => 99471
  14 => 99504
  15 => 99497
  16 => 99506
  17 => 99559
  18 => 99602
  19 => 99651
  20 => 99678
  21 => 99720
  22 => 99728
  23 => 99766
  24 => 99781
  25 => 99809
  26 => 99829
  27 => 99858
  28 => 99853
  29 => 99875
  30 => 99880
  31 => 99864
  32 => 99845
  33 => 99829
  34 => 99812
  35 => 99776
  36 => 99726
  37 => 99677
  38 => 99651
  39 => 99642
  40 => 99661
  41 => 99688
  42 => 99719
  43 => 99766
  44 => 99811
  45 => 99851
  46 => 99891
  47 => 99957
  48 => 100028
  49 => 100074
  50 => 100081
  51 => 100101
  52 => 100121
  53 => 100125
  54 => 100098
  55 => 100087
  56 => 100066
  57 => 100072
  58 => 100087
  59 => 100105
  60 => 100135
  61 => 100147
  62 => 100166
  63 => 100206
  64 => 100246
  65 => 100284
  66 => 100316
  67 => 100338
  68 => 100373
  69 => 100402
  70 => 100428
  71 => 100471
  72 => 100521
  73 => 100572
  74 => 100620
  75 => 100665
  76 => 100693
  77 => 100732
  78 => 100767
  79 => 100792
  80 => 100821
  81 => 100859
  82 => 100900
  83 => 100940
  84 => 100959
  85 => 100961
  86 => 100956
  87 => 100946
  88 => 100915
  89 => 100876
  90 => 100839
  91 => 100778
  92 => 100707
  93 => 100635
  94 => 100581
  95 => 100551
  96 => 100518
  97 => 100487
  98 => 100481
  99 => 100475
  100 => 100459
  101 => 100463
  102 => 100467
  103 => 100476
  104 => 100481
  105 => 100485
  106 => 100501
  107 => 100521
  108 => 100542
  109 => 100574
  110 => 100613
  111 => 100654
  112 => 100701
  113 => 100740
  114 => 100783
  115 => 100821
  116 => 100859
  ...

80 => 114 = 34 cycle repeat

1000000000 - 80 - 34x?

100876
 */
