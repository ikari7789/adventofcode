<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

enum HandType
{
    case FiveOfAKind;
    case FourOfAKind;
    case FullHouse;
    case ThreeOfAKind;
    case TwoPair;
    case OnePair;
    case HighCard;
}

class Hand
{
    private const CARD_VALUES = [
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        'T' => 10,
        'J' => 11,
        'Q' => 12,
        'K' => 13,
        'A' => 14,
    ];

    private ?HandType $type = null;

    public function __construct(
        readonly private array $cards,
        readonly private int $wager,
    ) {
    }

    public function cards(): array
    {
        return $this->cards;
    }

    public function wager(): int
    {
        return $this->wager;
    }

    public function cardScore(int $cardIndex): int
    {
        return self::CARD_VALUES[$this->cards[$cardIndex]];
    }

    public function type(): HandType
    {
        if ($this->type !== null) {
            return $this->type;
        }

        $cardCounts = array_count_values($this->cards);

        rsort($cardCounts);

        // five of a kind
        if ($cardCounts[0] === 5) {
            $this->type = HandType::FiveOfAKind;

            return $this->type;
        }

        // four of a kind
        if ($cardCounts[0] === 4) {
            $this->type = HandType::FourOfAKind;

            return $this->type;
        }

        // full house
        if ($cardCounts[0] === 3 && $cardCounts[1] === 2) {
            $this->type = HandType::FullHouse;

            return $this->type;
        }

        // three of a kind
        if ($cardCounts[0] === 3) {
            $this->type = HandType::ThreeOfAKind;

            return $this->type;
        }

        // two pair
        if ($cardCounts[0] === 2 && $cardCounts[1] === 2) {
            $this->type = HandType::TwoPair;

            return $this->type;
        }

        // one pair
        if ($cardCounts[0] === 2 && $cardCounts[1] === 1) {
            $this->type = HandType::OnePair;

            return $this->type;
        }

        // high card
        $this->type = HandType::HighCard;

        return $this->type;
    }
}

class CamelGame
{
    private const HAND_SIZE = 5;

    private array $hands = [];

    public function __construct()
    {
        $this->hands = [
            HandType::FiveOfAKind->name  => [],
            HandType::FourOfAKind->name  => [],
            HandType::FullHouse->name    => [],
            HandType::ThreeOfAKind->name => [],
            HandType::TwoPair->name      => [],
            HandType::OnePair->name      => [],
            HandType::HighCard->name     => [],
        ];
    }

    public function addHand(Hand $hand): void
    {
        $type = $hand->type();

        if (empty($this->hands[$type->name])) {
            $this->hands[$type->name][] = $hand;

            return;
        }

        $insertAt  = null;
        $handCount = count($this->hands[$type->name]);
        for ($handIndex = 0; $handIndex < $handCount; ++$handIndex) {
            $currentHand = $this->hands[$type->name][$handIndex];

            for ($cardIndex = 0; $cardIndex < self::HAND_SIZE; ++$cardIndex) {
                // Insert hand before this hand because current card is less than the card in the hand
                if ($hand->cardScore($cardIndex) < $currentHand->cardScore($cardIndex)) {
                    $insertAt = $handIndex;

                    break 2;
                }

                // Check next card because they're the same
                if ($hand->cardScore($cardIndex) === $currentHand->cardScore($cardIndex)) {
                    continue;
                }

                // Hand is stronger than current hand, go to the next hand
                if ($hand->cardScore($cardIndex) > $currentHand->cardScore($cardIndex)) {
                    continue 2;
                }
            }
            unset($cardIndex);
        }
        unset($handIndex, $currentHand);

        if ($insertAt === null) {
            $this->hands[$type->name][] = $hand;

            return;
        }

        array_splice($this->hands[$type->name], $insertAt, 0, [$hand]);

        return;
    }

    public function winnings(): int
    {
        $rank = 1;

        $winnings = 0;
        foreach (array_reverse(HandType::cases()) as $handType) {
            foreach ($this->hands[$handType->name] as $hand) {
                $winnings += $hand->wager() * $rank;
                ++$rank;
            }
        }

        return $winnings;
    }
}

$camelGame = new CamelGame();

$input = new SplFileObject(INPUT_FILE);
while (! $input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    [
        $cards,
        $wager,
    ] = explode(' ', $line);

    $camelGame->addHand(new Hand(str_split($cards), (int) $wager));
}

printf('Total winnings for game: %d' . PHP_EOL, $camelGame->winnings());
