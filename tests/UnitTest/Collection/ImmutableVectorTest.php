<?php


namespace Traver\Test\UnitTest\Collection;


use Traver\Collection\ImmutableVector;
use Traver\Collection\ImmutableVectorBuilder;

/**
 * Class ImmutableVectorTest
 * @package Traver\Test\UnitTest\Collection
 * @coversDefaultClass \Traver\Collection\ImmutableVector
 */
class ImmutableVectorTest extends AbstractVectorTest
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
        $vector = ImmutableVector::of(...$args);

        // then
        self::assertInstanceOf(ImmutableVector::class, $vector);
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
        $vector = ImmutableVector::copyOf($traversable);

        // then
        self::assertInstanceOf(ImmutableVector::class, $vector);
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
     * @depends testOf
     * @covers ::builder
     */
    public function testBuilder()
    {
        // given
        $vector = ImmutableVector::of();

        // when
        $builder = $vector->builder();

        // then
        self::assertInstanceOf(ImmutableVectorBuilder::class, $builder);
    }

    /**
     * @return \Traver\Collection\Builder
     */
    protected function createBuilder()
    {
        return ImmutableVector::newBuilder();
    }
}