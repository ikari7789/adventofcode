<?php

/**
 * @ref https://topaz.github.io/paste/#XQAAAQDQBQAAAAAAAAAeD8qHAhQB8nz+EazN00NCsmTxjvFDXkt5PAJ7pf6ocRA37OMUY7b9R0iDZmfWyvOspxWvdgYSAE0hGjEx81g50V6x3Jyy5AoVTW18qv9zWODTL9Vtxp4vBmGwDNhxh/Spa6xU38LleDZ2FOfAAzzg8LIsEWDKe+9IBCSnm2a5sxEgCNju1oFtbMmr9QbXDGG8uomiVTWcmVqEWogXGzUy0iOZEhkXTyu+tuql24yz/69B4NpZbjG8cCJQINvlOImn5bwqhv5i/DnzP4KonZ8MUF4LOmNxLH+FXuKYqOx7ZtTECqCXRTIqMxuRQqEKjmaCMW1rje8nTP7jwmMQKHgVAYaXKWnKJD81SCv0qbkrfHoJb3VS5QVB325bV2g07e7NFxC9jw0aCD1+fBIO6xA9hl9ptsU+JLUxCrDV6DOPZjCJSejgrXUp6v6bcJnbBS/5bmAj1W9RnP/Li69qGmNN9NWEANHCxA2XAoxcopTpWtr8E+JcpZVmz74bZ7Ctilt7HuKDkzG0Rh12kuDbVUOeunORNvMCysIjZh3Ye1XpCphACMGN0/I97iv+SiZHVwUDn1wvX+2MpQvVHKQioI7EGqSepqhYy000t/sSlFhKRV+QVdcx0aMo/IATOLxwzrIg6SCgTwigXJrEJ5uHYGgBTaUNfhNSxapd+21SgK8lcoaEaf9xpkMw8Xx7dLlwVuFJwiZU++P1723RCPF6oSxvR5EF3dSmRbILLJ1TsxQ9z7A9FmCyFlSO6UCVzLDXXU2rxz79fszWX9Dd2BE3VqomTEt2DL7sl+7p0tSpIMER3RMKwu5ulSN2J1OP9USstqncBY089coPo+jGuO2fTnKMvkbPxr+2KrWrqwanmBmLgaQCscYUhLyc6H8p0xQ8wxWrUfBwrMFo6nyhGsFKxVlf1A/61pokRRG+QQlZFgJK3rx4EE3M36wSKAGXg/vzvQh4p+Afzrx2U4c4to8aM1zrGf+2i6zY
 */

memory_reset_peak_usage();
$start_time = microtime(true);

$graph = file_get_contents("input.txt");
$graph = explode("\n", trim($graph));
$maxY = count($graph);
$maxX = strlen($graph[0]);

const MOVES = [[0, -1], [1, 0], [0, 1], [-1, 0]];

function f($start, $end, $minDistance = 1, $maxDistance = 3)
{
    global $graph, $maxY, $maxX;

    $V = [];
    $heap = new SplMinHeap();

    $heap->insert([0, [$start[0], $start[1], -1, 0]]);

    while (!$heap->isEmpty()) {
        [$distance, [$x, $y, $direction, $directionCost]] = $heap->extract();

        if ([$x, $y] == $end) {
            return $distance;
        }

        foreach (MOVES as $newDirection => $MOVE) {
            $newX = $x + $MOVE[0];
            $newY = $y + $MOVE[1];
            if ($newX < 0 || $newX >= $maxX || $newY < 0 || $newY >= $maxY || ($newDirection + 2) % 4 == $direction) {
                continue;
            }

            $newDirectionCost = ($newDirection == $direction ? $directionCost + 1 : 1);
            if ($newDirectionCost > $maxDistance || ($distance && $newDirection != $direction && $directionCost < $minDistance)) {
                continue;
            }
            $newDistance = $distance + (int)($graph[$newY][$newX]);

            $key = "$newX,$newY,$newDirection,$newDirectionCost";
            if ($newDistance < ($V[$key] ?? INF)) {
                $V[$key] = $newDistance;
                $heap->insert([$newDistance, [$newX, $newY, $newDirection, $newDirectionCost]]);
            }
        }
    }
    assert(false);
}

$part1 = f([0, 0], [$maxX - 1, $maxY - 1]);
$part2 = f([0, 0], [$maxX - 1, $maxY - 1], 4, 10);

echo "part 1: {$part1}\n";
echo "part 2: {$part2}\n";

echo "Execution time: " . round(microtime(true) - $start_time, 4) . " seconds\n";
echo "   Peak memory: " . round(memory_get_peak_usage() / pow(2, 20), 4), " MiB\n\n";
