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
     * @var bool
     */
    private $preserveKeys;
    /**
     * @var
     */
    private $index;

    /**
     * MappingIterator constructor.
     * @param Iterator $delegate
     * @param callable $mappingFunction
     * @param bool $preserveKeys
     */
    public function __construct(Iterator $delegate, callable $mappingFunction, $preserveKeys = true)
    {
        $this->delegate = $delegate;
        $this->mappingFunction = $mappingFunction;
        $this->preserveKeys = $preserveKeys;
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
        $this->index++;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return ($this->preserveKeys) ? $this->delegate->key() : $this->index;
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
        $this->index = 0;
    }

    /**
     * @inheritDoc
     */
    public function getInnerIterator()
    {
        return $this->delegate;
    }
}