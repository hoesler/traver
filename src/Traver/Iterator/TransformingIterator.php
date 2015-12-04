<?php

namespace Traver\Iterator;


use Iterator;
use OuterIterator;

class TransformingIterator implements OuterIterator
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
     * @var
     */
    private $index;

    /**
     * MappingIterator constructor.
     * @param Iterator $delegate
     * @param callable $mappingFunction
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
        $function = $this->mappingFunction;
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($key, $value) = $function($this->delegate->key(), $this->delegate->current(), $this->index);
        return $value;
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
        $function = $this->mappingFunction;
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($key, $value) = $function($this->delegate->key(), $this->delegate->current(), $this->index);
        return $key;
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