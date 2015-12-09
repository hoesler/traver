<?php


namespace Traver\Test\UnitTest\Enumerable;


use PHPUnit_Framework_TestCase;

trait MutabilityTest
{
    use BuildableCollectionTest;

    /**
     * @covers ::offsetSet
     */
    public function testSet()
    {
        // given
        $builder = $this->createBuilder();
        $collection = $builder->build();

        // when
        $collection[0] = 4;

        // then
        PHPUnit_Framework_TestCase::assertEquals(4, $collection[0]);
    }

    /**
     * @covers ::offsetUnset
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
        PHPUnit_Framework_TestCase::assertTrue(!isset($collection[0]));
    }
}