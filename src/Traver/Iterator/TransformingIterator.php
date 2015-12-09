<?php

namespace Traver\Iterator;


use Iterator;

class TransformingIterator extends ForwardingIterator
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
        list($key, $value) = $function(parent::key(), parent::current(), $this->index);
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        parent::next();
        $this->index++;
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        $function = $this->mappingFunction;
        /** @noinspection PhpUnusedLocalVariableInspection */
        list($key, $value) = $function(parent::key(), parent::current(), $this->index);
        return $key;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        parent::rewind();
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