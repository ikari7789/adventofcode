<?php

namespace Ikari7789\Adventofcode\Year2021\Tests\Day18;

use Ikari7789\Adventofcode\Year2021\Day18\SnailfishNumber;
use Ikari7789\Adventofcode\Year2021\Tests\TestCase;
use LogicException;

/**
 * @covers SnailfishNumber
 */
class SnailfishNumberTest extends TestCase
{
    /**
     * @dataProvider validNumberPairs
     */
    public function testCanCreateWithNumberPairs(array $numberPairs)
    {
        $this->assertInstanceOf(
            SnailfishNumber::class,
            new SnailfishNumber($numberPairs)
        );
    }

    /**
     * @dataProvider invalidNumberPairs
     */
    public function testCanOnlySendPairs(array $numberPairs)
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Only pairs allowed');

        new SnailfishNumber($numberPairs);
    }

    /**
     * @dataProvider nonIntegerNumberPairs
     */
    public function testCanOnlySendIntegers(array $numberPairs)
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Only integers allowed');

        new SnailfishNumber($numberPairs);
    }

    /**
     * @dataProvider reducableNumberPairs
     */
    public function testCanReduce(array $numberPairs, array $expectedOutput)
    {
        $snailfishNumber = new SnailfishNumber($numberPairs);

        $this->assertEquals($snailfishNumber->reduce(), $expectedOutput);
    }

    public function validNumberPairs()
    {
        return [
            'basic pair'           => [[1, 1]],
            'single embedded pair' => [[1, [1, 1]]],
            'two embedded pairs'   => [[[1, 1], [1, 1]]],
            'deep embedded pairs'  => [[1, [1, [1, 1]]]],
        ];
    }

    public function invalidNumberPairs()
    {
        return [
            'single number' => [[1]],
            'three numbers' => [[1, 2, 3]],
            'four numbers'  => [[1, 2, 3, 4]],
        ];
    }

    public function nonIntegerNumberPairs()
    {
        return [
            'float' => [[1.0, 1.0]],
            'alpha' => [['a', 'b']],
        ];
    }

    public function reducableNumberPairs()
    {
        return [
            [
                'input'           => [[[[[9, 8], 1], 2], 3], 4],
                'expected_output' => [[[[0, 9], 2], 3], 4],
            ],
            // [
            //     'input'           => [7, [6, [5, [4, [3, 2]]]]],
            //     'expected_output' => [7, [6, [5, [7, 0]]]],
            // ],
            // [
            //     'input'           => [[6, [5, [4, [3, 2]]]], 1],
            //     'expected_output' => [[6, [5, [7, 0]]], 3],
            // ],
            // [
            //     'input'           => [[3, [2, [1, [7, 3]]]], [6, [5, [4, [3, 2]]]]],
            //     'expected_output' => [[3, [2, [8, 0]]], [9, [5, [4, [3, 2]]]]],
            // ],
            // [
            //     'input'           => [[3, [2, [8, 0]]], [9, [5, [4, [3, 2]]]]],
            //     'expected_output' => [[3, [2, [8, 0]]], [9, [5, [7, 0]]]],
            // ],
        ];
    }
}