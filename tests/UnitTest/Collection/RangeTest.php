<?php


namespace Traver\Test\UnitTest\Collection;


use PHPUnit_Framework_TestCase;
use Traver\Collection\Range;

class RangeTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider toArrayProvider
     * @param $start
     * @param $end
     * @param $exclusive
     * @param $expected
     */
    public function testToArray($start, $end, $exclusive, $expected)
    {
        // given
        $range = new Range($start, $end, $exclusive);

        // when
        $array = $range->toArray();

        // then
        self::assertEquals($expected, $array);
    }

    public function toArrayProvider()
    {
        return [
            [0, 3, true, [0, 1, 2]],
            [0, 3, false, [0, 1, 2, 3]],
            ['a', 'd', false, ['a', 'b', 'c', 'd']]
        ];
    }
}