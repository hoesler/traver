<?php


namespace Traver\Test\UnitTest\Enumerable;


use Traver\Enumerable\MutableVector;

/**
 * Class MutableVectorTest
 * @package Traver\Test\UnitTest\Enumerable
 * @coversDefaultClass \Traver\Enumerable\MutableVector
 */
class MutableVectorTest extends AbstractVectorTest
{
    use MutabilityTest;

    /**
     * @return \Traver\Enumerable\Builder
     */
    protected function createBuilder()
    {
        $mutableVector = new MutableVector();
        return $mutableVector->builder();
    }
}