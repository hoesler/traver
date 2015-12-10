<?php

namespace Traver\Test\UnitTest\Collection;


use Traver\Collection\ImmutableMap;

/**
 * @coversDefaultClass \Traver\Collection\ImmutableMap
 */
class ImmutableMapTest extends AbstractMapTest
{
    use ImmutabilityTest;

    /**
     * @return \Traver\Collection\Builder
     */
    protected function createBuilder()
    {
        return ImmutableMap::newBuilder();
    }
}