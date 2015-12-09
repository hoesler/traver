<?php


namespace Traver\Test\UnitTest\Enumerable;


use Traver\Enumerable\ImmutableVector;

/**
 * Class ImmutableVectorTest
 * @package Traver\Test\UnitTest\Enumerable
 * @coversDefaultClass \Traver\Enumerable\ImmutableVector
 */
class ImmutableVectorTest extends AbstractVectorTest
{
    use ImmutabilityTest;

    /**
     * @return \Traver\Enumerable\Builder
     */
    protected function createBuilder()
    {
        return ImmutableVector::newBuilder();
    }
}