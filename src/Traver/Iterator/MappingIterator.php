<?php

namespace Traver\Iterator;


use Iterator;
use OuterIterator;

class MappingIterator implements OuterIterator
{
    /**
     * @var Iterator
     */
    private $delegate;
    /**
     * @var callable
     */
    private $mappingFunction;

    /**
     * MappingIterator constructor.
     * @param Iterator $delegate
     */
    public function __construct(Iterator $delegate, callable $mappingFunction)
    {
        $this->delegate = $delegate;
        $this->mappingFunction = $mappingFunction;
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return call_user_func($this->mappingFunction, $this->delegate->current(), $this->delegate->key());
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->delegate->next();
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->delegate->key();
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->delegate->valid();
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->delegate->rewind();
    }

    /**
     * @inheritDoc
     */
    public function getInnerIterator()
    {
        return $this->delegate;
    }
}