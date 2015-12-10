<?php


namespace Traver\Test\UnitTest\Collection;


use PHPUnit_Framework_TestCase;

trait ImmutabilityTest
{
    use BuildableCollectionTest;

    /**
     * @covers ::offsetSet
     * @expectedException \Traver\Exception\UnsupportedOperationException
     */
    public function testSetException()
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll([1, 2, 3]);
        $collection = $builder->build();

        // when
        $collection[0] = 4;

        // then
        PHPUnit_Framework_TestCase::fail();
    }

    /**
     * @covers ::offsetUnset
     * @expectedException \Traver\Exception\UnsupportedOperationException
     */
    public function testUnsetException()
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll([1, 2, 3]);
        $collection = $builder->build();

        // when
        unset($collection[0]);

        // then
        PHPUnit_Framework_TestCase::fail();
    }
}