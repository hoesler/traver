<?php


namespace Traver\Test\UnitTest\Collection;


use PHPUnit_Framework_TestCase;
use Traver\Collection\Range;

class RangeTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider toArrayProvider
     * @param Range $range
     * @param $expected
     */
    public function testToArray($range, $expected)
    {
        // when
        $array = $range->toArray();

        // then
        self::assertEquals($expected, $array);
    }

    public function toArrayProvider()
    {
        return [
            [new Range(0, 3, true), [0, 1, 2]],
            [new Range(0, 3, false), [0, 1, 2, 3]],
            [new Range('a', 'z', true), range('a', 'y')],
            [new Range('a', 'z', false), range('a', 'z')]
        ];
    }
}