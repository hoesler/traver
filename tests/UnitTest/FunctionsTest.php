<?php

namespace UnitTest;

use PHPUnit_Framework_TestCase;

class FunctionsTest extends PHPUnit_Framework_TestCase
{
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
}
