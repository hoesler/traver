<?php

namespace Traver\Test\UnitTest\Collection;


use Traver\Collection\ImmutableMap;
use Traver\Collection\ImmutableMapBuilder;

/**
 * @coversDefaultClass \Traver\Collection\ImmutableMap
 */
class ImmutableMapTest extends AbstractMapTest
{
    use ImmutabilityTest;

    /**
     * @dataProvider ofProvider
     * @covers ::of
     * @param $args
     */
    public function testOf(...$args)
    {
        // when
        $vector = ImmutableMap::of(...$args);

        // then
        self::assertInstanceOf(ImmutableMap::class, $vector);
        self::assertEquals($args, iterator_to_array($vector));
    }

    public function ofProvider()
    {
        return [
            [1, 2, 3],
            []
        ];
    }

    /**
     * @dataProvider copyOfProvider
     * @covers ::copyOf
     * @param $traversable
     */
    public function testCopyOf($traversable)
    {
        // when
        $vector = ImmutableMap::copyOf($traversable);

        // then
        self::assertInstanceOf(ImmutableMap::class, $vector);
        $expected = is_array($traversable) ? $traversable : iterator_to_array($traversable);
        $actual = iterator_to_array($vector);
        self::assertEquals($expected, $actual);
    }

    public function copyOfProvider()
    {
        return [
            [[1, 2, 3]],
            [[]],
            [new \ArrayObject([1, 2, 3])]
        ];
    }

    /**
     * @covers ::builder
     */
    public function testBuilder()
    {
        // given
        $mutableVector = ImmutableMap::of();

        // when
        $builder = $mutableVector->builder();

        // then
        self::assertInstanceOf(ImmutableMapBuilder::class, $builder);
    }

    /**
     * @return \Traver\Collection\Builder
     */
    protected function createBuilder()
    {
        return ImmutableMap::newBuilder();
    }
}