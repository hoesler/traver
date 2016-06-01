<?php


namespace Traver\Test\UnitTest\Collection;


use Traver\Collection\Pipeable;

trait BuildableCollectionTest
{
    /**
     * @return \Traver\Collection\Builder
     */
    abstract protected function createBuilder();

    /**
     * @param $array
     * @return Pipeable
     */
    final protected function createCollection($array)
    {
        return $this->createBuilder()->addAll($array)->build();
    }
}