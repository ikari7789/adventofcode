<?php

require __DIR__ . '/../../vendor/autoload.php';

use Delight\BaseConvert\Base;

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/input.txt');

class Packet
{
    private const PACKET_VERSION_INDEX = 0;
    private const PACKET_VERSION_SIZE  = 3;

    private const PACKET_TYPE_ID_INDEX = 0;
    private const PACKET_TYPE_ID_SIZE  = 3;

    private const PACKET_TYPE_SUM           = 0;
    private const PACKET_TYPE_PRODUCT       = 1;
    private const PACKET_TYPE_MINIMUM       = 2;
    private const PACKET_TYPE_MAXIMUM       = 3;
    private const PACKET_TYPE_LITERAL_VALUE = 4;
    private const PACKET_TYPE_GREATER_THAN  = 5;
    private const PACKET_TYPE_LESS_THAN     = 6;
    private const PACKET_TYPE_EQUAL_TO      = 7;

    private const OPERATOR_PACKET_TYPE_LENGTH_OF_SUBPACKETS            = 0;
    private const OPERATOR_PACKET_TYPE_LENGTH_OF_SUBPACKETS_FIELD_SIZE = 15;

    private const OPERATOR_PACKET_TYPE_NUMBER_OF_SUBPACKETS            = 1;
    private const OPERATOR_PACKET_TYPE_NUMBER_OF_SUBPACKETS_FIELD_SIZE = 11;

    private int $version;

    private int $typeId;

    private int $length = 0;

    private ?int $value = null;

    private array $subpackets = [];

    /**
     * Expects binary value array.
     */
    public function __construct(array $packet)
    {
        printf('Packet:  %s' . PHP_EOL, implode('', $packet));

        $this->version = $this->parseVersion($packet);
        $this->typeId  = $this->parseTypeId($packet);

        $this->parsePacket($packet);
        $this->process();
    }

    public function version(): int
    {
        return $this->version;
    }

    public function value(): ?int
    {
        return $this->value;
    }

    public function subpackets(): array
    {
        return $this->subpackets;
    }

    public function length(): int
    {
        return $this->length;
    }

    // Drill down till value !== null
    // Bubble upwards and set values
    public function process(): void
    {
        if ($this->value === null) {
            foreach ($this->subpackets() as $subpacket) {
                $subpacket->process();
            }
        }

        $value = null;
        switch ($this->typeId) {
            case self::PACKET_TYPE_SUM:
                printf('Packet type: %s' . PHP_EOL, 'sum');

                $value = 0;
                foreach ($this->subpackets as $subpacket) {
                    $value += $subpacket->value();
                }

                break;
            case self::PACKET_TYPE_PRODUCT:
                printf('Packet type: %s' . PHP_EOL, 'product');

                $value = 1;
                foreach ($this->subpackets as $subpacket) {
                    $value *= $subpacket->value();
                }

                break;
            case self::PACKET_TYPE_MINIMUM:
                printf('Packet type: %s' . PHP_EOL, 'minimum');

                $packet = array_reduce($this->subpackets, function($carry, $item) {
                    if ($carry === null) {
                        return $item;
                    }

                    if ($item->value() < $carry->value()) {
                        return $item;
                    }

                    return $carry;
                });
                $value = $packet->value();

                break;
            case self::PACKET_TYPE_MAXIMUM:
                printf('Packet type: %s' . PHP_EOL, 'maximum');

                $packet = array_reduce($this->subpackets, function($carry, $item) {
                    if ($carry === null) {
                        return $item;
                    }

                    if ($item->value() > $carry->value()) {
                        return $item;
                    }

                    return $carry;
                });
                $value = $packet->value();

                break;
            case self::PACKET_TYPE_GREATER_THAN:
                printf('Packet type: %s' . PHP_EOL, 'greater than');

                $value = 0;
                if ($this->subpackets[0]->value() > $this->subpackets[1]->value()) {
                    $value = 1;
                }

                break;
            case self::PACKET_TYPE_LESS_THAN:
                printf('Packet type: %s' . PHP_EOL, 'less than');

                $value = 0;
                if ($this->subpackets[0]->value() < $this->subpackets[1]->value()) {
                    $value = 1;
                }

                break;
            case self::PACKET_TYPE_EQUAL_TO:
                printf('Packet type: %s' . PHP_EOL, 'equal to');

                $value = 0;
                if ($this->subpackets[0]->value() === $this->subpackets[1]->value()) {
                    $value = 1;
                }

                break;
        }

        if ($value !== null) {
            $this->value = $value;
        }
    }

    private function parseVersion(array &$packet): int
    {
        $versionBinary = array_splice(
            $packet,
            self::PACKET_VERSION_INDEX,
            self::PACKET_VERSION_SIZE
        );

        $this->length += self::PACKET_VERSION_SIZE;

        printf('Version: %s' . PHP_EOL, implode('', $versionBinary));

        return Base::convert(implode('', $versionBinary), 2, 10);
    }

    private function parseTypeId(array &$packet): int
    {
        $typeIdBinary = array_splice(
            $packet,
            self::PACKET_TYPE_ID_INDEX,
            self::PACKET_TYPE_ID_SIZE
        );

        $this->length += self::PACKET_TYPE_ID_SIZE;

        printf('Type id: %s' . PHP_EOL, implode('', $typeIdBinary));

        return Base::convert(implode('', $typeIdBinary), 2, 10);
    }

    private function parsePacket(array &$packet): void
    {
        if ($this->typeId === self::PACKET_TYPE_LITERAL_VALUE) {
            $this->value = $this->parseLiteralValuePacket($packet);

            return;
        }

        $this->parseOperatorPacket($packet);

        return;
    }

    private function parseLiteralValuePacket(array &$packet)
    {
        $value = [];

        do {
            $lastGroupFlag = array_shift($packet);
            $value         = array_merge($value, array_splice($packet, 0, 4));
            $this->length += 5;
        } while ($lastGroupFlag !== 0);

        $value = Base::convert(implode('', $value), 2, 10);

        printf('Value:   %s' . PHP_EOL, $value);

        return $value;
    }

    private function parseOperatorPacket(array &$packet)
    {
        printf('Packet:  %s' . PHP_EOL, implode('', $packet));

        $lengthTypeId = array_shift($packet);

        ++$this->length;

        if ($lengthTypeId === self::OPERATOR_PACKET_TYPE_LENGTH_OF_SUBPACKETS) {
            $this->parseOperatorLengthPacket($packet);
        } elseif ($lengthTypeId === self::OPERATOR_PACKET_TYPE_NUMBER_OF_SUBPACKETS) {
            $this->parseOperatorNumberPacket($packet);
        }
    }

    private function parseOperatorLengthPacket(array &$packet)
    {
        $lengthOfPacketsBinary = array_splice(
            $packet,
            0,
            self::OPERATOR_PACKET_TYPE_LENGTH_OF_SUBPACKETS_FIELD_SIZE
        );

        printf('Length of packets: %s' . PHP_EOL, implode('', $lengthOfPacketsBinary));

        $lengthOfPackets = (int) Base::convert(
            implode(
                '',
                $lengthOfPacketsBinary
            ),
            2,
            10,
        );

        $this->length += self::OPERATOR_PACKET_TYPE_LENGTH_OF_SUBPACKETS_FIELD_SIZE;

        printf('Length of packets: %d' . PHP_EOL, $lengthOfPackets);

        $subpackets = array_splice($packet, 0, $lengthOfPackets);

        printf('Subpackets: %s' . PHP_EOL, implode('', $subpackets));

        $processLength = 0;
        while (count($subpackets) > 0) {
            $subpacket = new Packet($subpackets);
            $this->subpackets[] = $subpacket;
            array_splice($subpackets, 0, $subpacket->length());
            $this->length += $subpacket->length();
        }
    }

    private function parseOperatorNumberPacket(array &$packet)
    {
        $numberOfPackets = (int) Base::convert(
            implode(
                '',
                array_splice(
                    $packet,
                    0,
                    self::OPERATOR_PACKET_TYPE_NUMBER_OF_SUBPACKETS_FIELD_SIZE
                )
            ),
            2,
            10,
        );

        $this->length += self::OPERATOR_PACKET_TYPE_NUMBER_OF_SUBPACKETS_FIELD_SIZE;

        printf('Number of packets: %d' . PHP_EOL, $numberOfPackets);

        $processLength = 0;
        do {
            $subpacket = new Packet($packet);
            $this->subpackets[] = $subpacket;
            array_splice($packet, 0, $subpacket->length());
            $this->length += $subpacket->length();
            printf('Number of subpackets: %d' . PHP_EOL, count($this->subpackets));
        } while (count($this->subpackets) !== $numberOfPackets);;
    }
}

function sumVersions(Packet $packet, int &$sum = 0): void
{
    $sum += $packet->version();

    foreach ($packet->subpackets() as $subpacket) {
        sumVersions($subpacket, $sum);
    }
}

function hexToBin(string $hex)
{
    $binary = '';

    foreach (str_split($hex) as $char) {
        $binary .= match ($char) {
            '0' => '0000',
            '1' => '0001',
            '2' => '0010',
            '3' => '0011',
            '4' => '0100',
            '5' => '0101',
            '6' => '0110',
            '7' => '0111',
            '8' => '1000',
            '9' => '1001',
            'A' => '1010',
            'B' => '1011',
            'C' => '1100',
            'D' => '1101',
            'E' => '1110',
            'F' => '1111',
        };
    }

    return $binary;
}

$input = new SplFileObject(INPUT_FILE);

$hex    = trim($input->fgets());
$binary = array_map('intval', str_split(hexToBin($hex)));

printf('Hex:     %s' . PHP_EOL, $hex);
printf('Binary:  %s' . PHP_EOL, implode('', $binary));

$packet = new Packet($binary);

$versionSum = 0;
sumVersions($packet, $versionSum);
printf('Sum of versions: %d' . PHP_EOL, $versionSum);
printf('Packet value:    %d' . PHP_EOL, $packet->value());
