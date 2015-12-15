<?php


namespace Traver\Test\UnitTest\Collection;


use Traver\Collection\MutableMap;
use Traver\Collection\MutableMapBuilder;

/**
 * Class MutableMapTest
 * @package Traver\Test\UnitTest\Collection
 * @coversDefaultClass \Traver\Collection\MutableMap
 */
class MutableMapTest extends AbstractMapTest
{
    use MutabilityTest;

    /**
     * @covers ::builder
     */
    public function testBuilder()
    {
        // given
        $mutableVector = new MutableMap();

        // when
        $builder = $mutableVector->builder();

        // then
        self::assertInstanceOf(MutableMapBuilder::class, $builder);
    }

    /**
     * @return \Traver\Collection\Builder
     */
    protected function createBuilder()
    {
        $arrayObjectEnumerable = new MutableMap();
        return $arrayObjectEnumerable->builder();
    }
}