<?php

namespace Traver\Test\UnitTest\Enumerable;


use Traver\Enumerable\ImmutableMap;

/**
 * @coversDefaultClass \Traver\Enumerable\ImmutableMap
 */
class ImmutableMapTest extends AbstractMapTest
{
    use ImmutabilityTest;

    /**
     * @return \Traver\Enumerable\Builder
     */
    protected function createBuilder()
    {
        return ImmutableMap::newBuilder();
    }
}