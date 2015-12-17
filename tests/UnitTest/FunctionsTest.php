<?php

namespace Traver\Test\UnitTest;

use PHPUnit_Framework_TestCase;
use Traver\Collection\ImmutableMap;
use Traver\Collection\ImmutableVector;
use Traver\Collection\MutableMap;
use Traver\Collection\MutableVector;

class FunctionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider hashProvider
     * @covers ::\Traver\hash
     * @param $input
     */
    public function testHash($input)
    {
        // given
        $array = $input;

        // when
        $view = \Traver\map($array);

        // then
        self::assertInstanceOf(ImmutableMap::class, $view);
    }

    public function hashProvider()
    {
        return [
            [["a", "b", "c"]],
            [["a" => 1, "b" => 2, "c" => 3]]
        ];
    }

    /**
     * @dataProvider mutableHashProvider
     * @covers ::\Traver\mutable_hash
     * @param $input
     */
    public function testMutableHash($input)
    {
        // given
        $array = $input;

        // when
        $view = \Traver\mutable_map($array);

        // then
        self::assertInstanceOf(MutableMap::class, $view);
    }

    public function mutableHashProvider()
    {
        return [
            [["a", "b", "c"]],
            [["a" => 1, "b" => 2, "c" => 3]]
        ];
    }

    /**
     * @dataProvider vectorProvider
     * @covers ::\Traver\hash
     * @param $input
     */
    public function testVector($input)
    {
        // given
        $array = $input;

        // when
        $view = \Traver\vector($array);

        // then
        self::assertInstanceOf(ImmutableVector::class, $view);
    }

    public function vectorProvider()
    {
        return [
            [["a", "b", "c"]]
        ];
    }

    /**
     * @dataProvider mutableVectorProvider
     * @covers ::\Traver\mutable_hash
     * @param $input
     */
    public function testMutableVector($input)
    {
        // given
        $array = $input;

        // when
        $view = \Traver\mutable_vector($array);

        // then
        self::assertInstanceOf(MutableVector::class, $view);
    }

    public function mutableVectorProvider()
    {
        return [
            [["a", "b", "c"]]
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
        self::assertInstanceOf(ImmutableMap::class, $view);
    }

    public function inProvider()
    {
        return [
            [["a", "b", "c"]],
            [new \ArrayObject(["a", "b", "c"])]
        ];
    }
}
