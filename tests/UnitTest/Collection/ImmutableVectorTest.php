<?php


namespace Traver\Test\UnitTest\Collection;


use Traver\Collection\ImmutableVector;

/**
 * Class ImmutableVectorTest
 * @package Traver\Test\UnitTest\Collection
 * @coversDefaultClass \Traver\Collection\ImmutableVector
 */
class ImmutableVectorTest extends AbstractVectorTest
{
    use ImmutabilityTest;

    /**
     * @return \Traver\Collection\Builder
     */
    protected function createBuilder()
    {
        return ImmutableVector::newBuilder();
    }
}