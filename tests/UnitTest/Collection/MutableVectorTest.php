<?php


namespace Traver\Test\UnitTest\Collection;


use Traver\Collection\MutableVector;
use Traver\Collection\MutableVectorBuilder;

/**
 * Class MutableVectorTest
 * @package Traver\Test\UnitTest\Collection
 * @coversDefaultClass \Traver\Collection\MutableVector
 */
class MutableVectorTest extends AbstractVectorTest
{
    use MutabilityTest;

    /**
     * @covers ::builder
     */
    public function testBuilder()
    {
        // given
        $mutableVector = new MutableVector();

        // when
        $builder = $mutableVector->builder();

        // then
        self::assertInstanceOf(MutableVectorBuilder::class, $builder);
    }

    /**
     * @return \Traver\Collection\Builder
     */
    protected function createBuilder()
    {
        $mutableVector = new MutableVector();
        return $mutableVector->builder();
    }


}