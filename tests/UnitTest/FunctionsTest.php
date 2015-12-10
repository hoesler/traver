<?php

namespace Traver\Test\UnitTest;

use PHPUnit_Framework_TestCase;
use Traver\Collection\Enumerable;

class FunctionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider viewProvider
     * @covers ::\Traver\view
     * @param $input
     */
    public function testView($input)
    {
        // given
        $array = $input;

        // when
        $view = \Traver\view($array);

        // then
        self::assertInstanceOf(Enumerable::class, $view);
    }

    public function viewProvider()
    {
        return [
            [["a", "b", "c"]],
            [new \ArrayObject(["a", "b", "c"])]
        ];
    }

    /**
     * @dataProvider inProvider
     * @covers ::\Traver\view
     * @param $input
     */
    public function testIn($input)
    {
        // given
        $array = $input;

        // when
        $view = \Traver\in($array);

        // then
        self::assertInstanceOf(Enumerable::class, $view);
    }

    public function inProvider()
    {
        return [
            [["a", "b", "c"]],
            [new \ArrayObject(["a", "b", "c"])]
        ];
    }

    /**
     * @covers ::\Traver\head
     */
    public function test_head()
    {
        // given
        $array = ["a", "b", "c"];

        // when
        $head = \Traver\head($array);

        // then
        self::assertEquals('a', $head);
    }

    /**
     * @covers ::\Traver\tail
     */
    public function test_tail()
    {
        // given
        $array = ["a", "b", "c"];

        // when
        $tail = \Traver\tail($array);

        // then
        self::assertEquals(array_slice($array, 1, null, true), $tail);
    }

    /**
     * @covers ::\Traver\map
     */
    public function test_map()
    {
        // given
        $array = ["a", "b", "c"];
        $mappingFunction = function ($item) {
            return $item . "_";
        };

        // when
        $mapped = \Traver\map($array, $mappingFunction);

        // then
        self::assertEquals(array_map($mappingFunction, $array), $mapped);
    }

    /**
     * @covers ::\Traver\reduce
     */
    public function test_reduce()
    {
        // given
        $array = [1, 2, 3];

        // when
        $reduced = \Traver\reduce($array, \Traver\op("+"));

        // then
        self::assertEquals(6, $reduced);
    }
}
