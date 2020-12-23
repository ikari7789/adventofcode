<?php

$inputFile = '11.txt';

/**
 * --- Day 11: Seating System ---
 *
 * Your plane lands with plenty of time to spare. The final leg of your
 * journey is a ferry that goes directly to the tropical island where you can
 * finally start your vacation. As you reach the waiting area to board the
 * ferry, you realize you're so early, nobody else has even arrived yet!
 *
 * By modeling the process people use to choose (or abandon) their seat in the
 * waiting area, you're pretty sure you can predict the best place to sit. You
 * make a quick map of the seat layout (your puzzle input).
 *
 * The seat layout fits neatly on a grid. Each position is either floor (.),
 * an empty seat (L), or an occupied seat (#). For example, the initial seat
 * layout might look like this:
 *
 * ```
 * L.LL.LL.LL
 * LLLLLLL.LL
 * L.L.L..L..
 * LLLL.LL.LL
 * L.LL.LL.LL
 * L.LLLLL.LL
 * ..L.L.....
 * LLLLLLLLLL
 * L.LLLLLL.L
 * L.LLLLL.LL
 * ```
 *
 * Now, you just need to model the people who will be arriving shortly.
 * Fortunately, people are entirely predictable and always follow a simple set
 * of rules. All decisions are based on the number of occupied seats adjacent
 * to a given seat (one of the eight positions immediately up, down, left,
 * right, or diagonal from the seat). The following rules are applied to every
 * seat simultaneously:
 *
 *   - If a seat is empty (L) and there are no occupied seats adjacent to it,
 *     the seat becomes occupied.
 *   - If a seat is occupied (#) and four or more seats adjacent to it are
 *     also occupied, the seat becomes empty.
 *   - Otherwise, the seat's state does not change.
 *
 * Floor (.) never changes; seats don't move, and nobody sits on the floor.
 *
 * After one round of these rules, every seat in the example layout becomes
 * occupied:
 *
 * ```
 * #.##.##.##
 * #######.##
 * #.#.#..#..
 * ####.##.##
 * #.##.##.##
 * #.#####.##
 * ..#.#.....
 * ##########
 * #.######.#
 * #.#####.##
 * ```
 *
 * After a second round, the seats with four or more occupied adjacent seats
 * become empty again:
 *
 * ```
 * #.LL.L#.##
 * #LLLLLL.L#
 * L.L.L..L..
 * #LLL.LL.L#
 * #.LL.LL.LL
 * #.LLLL#.##
 * ..L.L.....
 * #LLLLLLLL#
 * #.LLLLLL.L
 * #.#LLLL.##
 * ```
 *
 * This process continues for three more rounds:
 *
 * ```
 * #.##.L#.##
 * #L###LL.L#
 * L.#.#..#..
 * #L##.##.L#
 * #.##.LL.LL
 * #.###L#.##
 * ..#.#.....
 * #L######L#
 * #.LL###L.L
 * #.#L###.##
 * ```
 *
 * ```
 * #.#L.L#.##
 * #LLL#LL.L#
 * L.L.L..#..
 * #LLL.##.L#
 * #.LL.LL.LL
 * #.LL#L#.##
 * ..L.L.....
 * #L#LLLL#L#
 * #.LLLLLL.L
 * #.#L#L#.##
 * ```
 *
 * ```
 * #.#L.L#.##
 * #LLL#LL.L#
 * L.#.L..#..
 * #L##.##.L#
 * #.#L.LL.LL
 * #.#L#L#.##
 * ..L.L.....
 * #L#L##L#L#
 * #.LLLLLL.L
 * #.#L#L#.##
 * ```
 *
 * At this point, something interesting happens: the chaos stabilizes and
 * further applications of these rules cause no seats to change state! Once
 * people stop moving around, you count 37 occupied seats.
 *
 * Simulate your seating area by applying the seating rules repeatedly until
 * no seats change state. How many seats end up occupied?
 */

$input = new SplFileObject($inputFile);

$seatingMatrix = [];
while (! $input->eof()) {
    $line = $input->fgets();
    $line = trim($line);

    if (empty($line)) {
        continue;
    }

    $seatingMatrix[] = str_split($line);
}

function seatExists(?string $seat = null): bool
{
    return $seat === null;
}

function seatIsEmpty(?string $seat = null): bool
{
    return in_array($seat, ['L', '.']);
}

function seatsAreEmpty(array $seats = []): int
{
    $emptySeats = 0;
    foreach ($seats as $seat) {
        if (seatIsEmpty($seat)) {
            ++$emptySeats;
        }
    }

    return $emptySeats;
}

function seatsAreFilled(array $seats = []): int
{
    return count($seats) - seatsAreEmpty($seats);
}

function updateSeating(array $seatingMatrix = [])
{
    $newSeatingMatrix = $seatingMatrix;
    for ($row = 0; $row < count($seatingMatrix); ++$row) {
        for ($column = 0; $column < count($seatingMatrix[$row]); ++$column) {
            $left        = null;
            $topLeft     = null;
            $top         = null;
            $topRight    = null;
            $right       = null;
            $bottomRight = null;
            $bottom      = null;
            $bottomLeft  = null;

            if ($column - 1 >= 0) {
                $left = $seatingMatrix[$row][$column - 1];
            }

            if ($row - 1 >= 0 && $column - 1 >= 0) {
                $topLeft = $seatingMatrix[$row - 1][$column - 1];
            }

            if ($row - 1 >= 0) {
                $top = $seatingMatrix[$row - 1][$column];
            }

            if ($row - 1 >= 0 && $column + 1 < count($seatingMatrix[$row - 1])) {
                $topRight = $seatingMatrix[$row - 1][$column + 1];
            }

            if ($column + 1 < count($seatingMatrix[$row])) {
                $right = $seatingMatrix[$row][$column + 1];
            }

            if ($row + 1 < count($seatingMatrix) && $column + 1 < count($seatingMatrix[$row + 1])) {
                $bottomRight = $seatingMatrix[$row + 1][$column + 1];
            }

            if ($row + 1 < count($seatingMatrix)) {
                $bottom = $seatingMatrix[$row + 1][$column];
            }

            if ($row + 1 < count($seatingMatrix) && $column - 1 >= 0) {
                $bottomLeft = $seatingMatrix[$row + 1][$column - 1];
            }

            $adjacentSeats = array_filter([
                $left,
                $topLeft,
                $top,
                $topRight,
                $right,
                $bottomRight,
                $bottom,
                $bottomLeft,
            ], fn($value) => ! is_null($value));

            switch ($seatingMatrix[$row][$column]) {
                case 'L':
                    if (seatsAreEmpty($adjacentSeats) === count($adjacentSeats)) {
                        $newSeatingMatrix[$row][$column] = '#';
                    }
                    break;
                case '#':
                    if (seatsAreFilled($adjacentSeats) >= 4) {
                        $newSeatingMatrix[$row][$column] = 'L';
                    }
                    break;
                case '.':

                    break;
            }
        }
    }

    return $newSeatingMatrix;
}

function printSeating(array $seatingMatrix = [])
{
    for ($row = 0; $row < count($seatingMatrix); ++$row) {
        for ($column = 0; $column < count($seatingMatrix[$row]); ++$column) {
            echo $seatingMatrix[$row][$column];
        }
        echo PHP_EOL;
    }
}

function seatingsMatch($seatingMatrix1, $seatingMatrix2)
{
    for ($row = 0; $row < count($seatingMatrix1); ++$row) {
        for ($column = 0; $column < count($seatingMatrix1[$row]); ++$column) {
            if ($seatingMatrix1[$row][$column] !== $seatingMatrix2[$row][$column]) {
                return false;
            }
        }
    }

    return true;
}

function occupiedSeats($seatingMatrix)
{
    $occupiedSeats = 0;
    for ($row = 0; $row < count($seatingMatrix); ++$row) {
        for ($column = 0; $column < count($seatingMatrix[$row]); ++$column) {
            if ($seatingMatrix[$row][$column] === '#') {
                ++$occupiedSeats;
            }
        }
    }
    return $occupiedSeats;
}

$initial = $seatingMatrix;

echo 'Initial State'.PHP_EOL;
printSeating($initial);
echo PHP_EOL;

$updated = updateSeating($initial);
while (! seatingsMatch($initial, $updated)) {
    $initial = $updated;
    $updated = updateSeating($initial);
}

echo 'Equalized State'.PHP_EOL;
printSeating($updated);
echo PHP_EOL;

echo 'Occupied Seats: '.occupiedSeats($updated);
echo PHP_EOL;

/**
 * --- Part Two ---
 *
 * As soon as people start to arrive, you realize your mistake. People don't
 * just care about adjacent seats - they care about the first seat they can
 * see in each of those eight directions!
 *
 * Now, instead of considering just the eight immediately adjacent seats,
 * consider the first seat in each of those eight directions. For example, the
 * empty seat below would see eight occupied seats:
 *
 * ```
 * .......#.
 * ...#.....
 * .#.......
 * .........
 * ..#L....#
 * ....#....
 * .........
 * #........
 * ...#.....
 * ```
 *
 * The leftmost empty seat below would only see one empty seat, but cannot see
 * any of the occupied ones:
 *
 * ```
 * .............
 * .L.L.#.#.#.#.
 * .............
 * ```
 *
 * The empty seat below would see no occupied seats:
 *
 * ```
 * .##.##.
 * #.#.#.#
 * ##...##
 * ...L...
 * ##...##
 * #.#.#.#
 * .##.##.
 * ```
 *
 * Also, people seem to be more tolerant than you expected: it now takes five
 * or more visible occupied seats for an occupied seat to become empty (rather
 * than four or more from the previous rules). The other rules still apply:
 * empty seats that see no occupied seats become occupied, seats matching no
 * rule don't change, and floor never changes.
 *
 * Given the same starting layout as above, these new rules cause the seating
 * area to shift around as follows:
 *
 * ```
 * L.LL.LL.LL
 * LLLLLLL.LL
 * L.L.L..L..
 * LLLL.LL.LL
 * L.LL.LL.LL
 * L.LLLLL.LL
 * ..L.L.....
 * LLLLLLLLLL
 * L.LLLLLL.L
 * L.LLLLL.LL
 * ```
 *
 * ```
 * #.##.##.##
 * #######.##
 * #.#.#..#..
 * ####.##.##
 * #.##.##.##
 * #.#####.##
 * ..#.#.....
 * ##########
 * #.######.#
 * #.#####.##
 * ```
 *
 * ```
 * #.LL.LL.L#
 * #LLLLLL.LL
 * L.L.L..L..
 * LLLL.LL.LL
 * L.LL.LL.LL
 * L.LLLLL.LL
 * ..L.L.....
 * LLLLLLLLL#
 * #.LLLLLL.L
 * #.LLLLL.L#
 * ```
 *
 * ```
 * #.L#.##.L#
 * #L#####.LL
 * L.#.#..#..
 * ##L#.##.##
 * #.##.#L.##
 * #.#####.#L
 * ..#.#.....
 * LLL####LL#
 * #.L#####.L
 * #.L####.L#
 * ```
 *
 * ```
 * #.L#.L#.L#
 * #LLLLLL.LL
 * L.L.L..#..
 * ##LL.LL.L#
 * L.LL.LL.L#
 * #.LLLLL.LL
 * ..L.L.....
 * LLLLLLLLL#
 * #.LLLLL#.L
 * #.L#LL#.L#
 * ```
 *
 * ```
 * #.L#.L#.L#
 * #LLLLLL.LL
 * L.L.L..#..
 * ##L#.#L.L#
 * L.L#.#L.L#
 * #.L####.LL
 * ..#.#.....
 * LLL###LLL#
 * #.LLLLL#.L
 * #.L#LL#.L#
 * ```
 *
 * ```
 * #.L#.L#.L#
 * #LLLLLL.LL
 * L.L.L..#..
 * ##L#.#L.L#
 * L.L#.LL.L#
 * #.LLLL#.LL
 * ..#.L.....
 * LLL###LLL#
 * #.LLLLL#.L
 * #.L#LL#.L#
 * ```
 *
 * Again, at this point, people stop shifting around and the seating area
 * reaches equilibrium. Once this occurs, you count 26 occupied seats.
 *
 * Given the new visibility method and the rule change for occupied seats
 * becoming empty, once equilibrium is reached, how many seats end up
 * occupied?
 */

function nearestLeftSeat($seatingMatrix, $row, $column)
{
    // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
    while ($column > 0) {
        --$column;
        // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
        if ($seatingMatrix[$row][$column] !== '.') {
            return $seatingMatrix[$row][$column];
        }
    }

    return null;
}

function nearestTopLeftSeat($seatingMatrix, $row, $column)
{
    // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
    while ($row > 0 && $column > 0) {
        --$row;
        --$column;
        // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
        if ($seatingMatrix[$row][$column] !== '.') {
            return $seatingMatrix[$row][$column];
        }
    }

    return null;
}

function nearestTopSeat($seatingMatrix, $row, $column)
{
    // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
    while ($row > 0) {
        --$row;
        // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
        if ($seatingMatrix[$row][$column] !== '.') {
            return $seatingMatrix[$row][$column];
        }
    }

    return null;
}

function nearestTopRightSeat($seatingMatrix, $row, $column)
{
    // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
    while ($row > 0 && $column < count($seatingMatrix[$row]) - 1) {
        --$row;
        ++$column;
        // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
        if ($seatingMatrix[$row][$column] !== '.') {
            return $seatingMatrix[$row][$column];
        }
    }

    return null;
}

function nearestRightSeat($seatingMatrix, $row, $column)
{
    // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
    while ($column < count($seatingMatrix[$row]) - 1) {
        ++$column;
        // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
        if ($seatingMatrix[$row][$column] !== '.') {
            return $seatingMatrix[$row][$column];
        }
    }

    return null;
}

function nearestBottomRightSeat($seatingMatrix, $row, $column)
{
    // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
    while ($row < count($seatingMatrix) - 1 && $column < count($seatingMatrix[$row]) - 1) {
        ++$row;
        ++$column;
        // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
        if ($seatingMatrix[$row][$column] !== '.') {
            return $seatingMatrix[$row][$column];
        }
    }

    return null;
}

function nearestBottomSeat($seatingMatrix, $row, $column)
{
    // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
    while ($row < count($seatingMatrix) - 1) {
        ++$row;
        // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
        if ($seatingMatrix[$row][$column] !== '.') {
            return $seatingMatrix[$row][$column];
        }
    }

    return null;
}

function nearestBottomLeftSeat($seatingMatrix, $row, $column)
{
    // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
    while ($row < count($seatingMatrix) - 1 && $column > 0) {
        ++$row;
        --$column;
        // printf('Row: %d, Column: %d'.PHP_EOL, $row, $column);
        if ($seatingMatrix[$row][$column] !== '.') {
            return $seatingMatrix[$row][$column];
        }
    }

    return null;
}

function updateSeating2(array $seatingMatrix = [])
{
    $newSeatingMatrix = $seatingMatrix;
    for ($row = 0; $row < count($seatingMatrix); ++$row) {
        for ($column = 0; $column < count($seatingMatrix[$row]); ++$column) {
            $left        = nearestLeftSeat($seatingMatrix, $row, $column);
            $topLeft     = nearestTopLeftSeat($seatingMatrix, $row, $column);
            $top         = nearestTopSeat($seatingMatrix, $row, $column);
            $topRight    = nearestTopRightSeat($seatingMatrix, $row, $column);;
            $right       = nearestRightSeat($seatingMatrix, $row, $column);;
            $bottomRight = nearestBottomRightSeat($seatingMatrix, $row, $column);;
            $bottom      = nearestBottomSeat($seatingMatrix, $row, $column);;
            $bottomLeft  = nearestBottomLeftSeat($seatingMatrix, $row, $column);;

            $adjacentSeats = array_filter([
                $left,
                $topLeft,
                $top,
                $topRight,
                $right,
                $bottomRight,
                $bottom,
                $bottomLeft,
            ], fn($value) => ! is_null($value));

            // if ($seatingMatrix[$row][$column] === 'L') {
            //     printf('Row: %d, Column: %d (%s)'.PHP_EOL, $row, $column, $seatingMatrix[$row][$column]);
            //     echo '$left        = '.$left.PHP_EOL;
            //     echo '$topLeft     = '.$topLeft.PHP_EOL;
            //     echo '$top         = '.$top.PHP_EOL;
            //     echo '$topRight    = '.$topRight.PHP_EOL;
            //     echo '$right       = '.$right.PHP_EOL;
            //     echo '$bottomRight = '.$bottomRight.PHP_EOL;
            //     echo '$bottom      = '.$bottom.PHP_EOL;
            //     echo '$bottomLeft  = '.$bottomLeft.PHP_EOL;
            //     echo PHP_EOL;
            // }

            switch ($seatingMatrix[$row][$column]) {
                case 'L':
                    if (seatsAreEmpty($adjacentSeats) === count($adjacentSeats)) {
                        $newSeatingMatrix[$row][$column] = '#';
                    }
                    break;
                case '#':
                    if (seatsAreFilled($adjacentSeats) >= 5) {
                        $newSeatingMatrix[$row][$column] = 'L';
                    }
                    break;
                case '.':

                    break;
            }
        }
    }

    return $newSeatingMatrix;
}

$initial = $seatingMatrix;

echo 'Initial State'.PHP_EOL;
printSeating($initial);
echo PHP_EOL;

$updated = updateSeating2($initial);
while (! seatingsMatch($initial, $updated)) {
    $initial = $updated;
    $updated = updateSeating2($initial);
}

echo 'Equalized State'.PHP_EOL;
printSeating($updated);
echo PHP_EOL;

echo 'Occupied Seats: '.occupiedSeats($updated);
echo PHP_EOL;