<?php


namespace Traver\Test\UnitTest\Enumerable;


use Traver\Enumerable\MutableMap;

/**
 * Class MutableMapTest
 * @package Traver\Test\UnitTest\Enumerable
 * @coversDefaultClass \Traver\Enumerable\MutableMap
 */
class MutableMapTest extends AbstractMapTest
{
    use MutabilityTest;

    /**
     * @return \Traver\Enumerable\Builder
     */
    protected function createBuilder()
    {
        $arrayObjectEnumerable = new MutableMap();
        return $arrayObjectEnumerable->builder();
    }
}