<?php


namespace Traver\Test\UnitTest\Collection;


use Traver\Collection\MutableMap;

/**
 * Class MutableMapTest
 * @package Traver\Test\UnitTest\Collection
 * @coversDefaultClass \Traver\Collection\MutableMap
 */
class MutableMapTest extends AbstractMapTest
{
    use MutabilityTest;

    /**
     * @return \Traver\Collection\Builder
     */
    protected function createBuilder()
    {
        $arrayObjectEnumerable = new MutableMap();
        return $arrayObjectEnumerable->builder();
    }
}