<?php


namespace Traver\Test\UnitTest\Collection;


use Traver\Collection\MutableVector;

/**
 * Class MutableVectorTest
 * @package Traver\Test\UnitTest\Collection
 * @coversDefaultClass \Traver\Collection\MutableVector
 */
class MutableVectorTest extends AbstractVectorTest
{
    use MutabilityTest;

    /**
     * @return \Traver\Collection\Builder
     */
    protected function createBuilder()
    {
        $mutableVector = new MutableVector();
        return $mutableVector->builder();
    }
}