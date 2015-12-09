<?php


namespace Traver\Iterator;


use EmptyIterator;
use Iterator;

final class ConcatIterator implements \OuterIterator
{
    /**
     * @var Iterator
     */
    private $iterator;
    /**
     * @var Iterator
     */
    private $current;
    private $key = 0;

    public function __construct(Iterator $iterator)
    {
        $this->current = new EmptyIterator();
        $this->iterator = $iterator;
    }

    public function rewind()
    {
        $this->iterator->rewind();
        $this->current = new EmptyIterator();
        $this->key = 0;
    }

    public function current()
    {
        if (!$this->current->valid()) {
            $this->findNextIterator();
        }
        return $this->current->current();
    }

    private function findNextIterator()
    {
        while (!$this->current->valid() && $this->iterator->valid()) {
            $this->current = $this->iterator->current();
            $this->iterator->next();
        }
    }

    public function valid()
    {
        if (!$this->current->valid()) {
            $this->findNextIterator();
        }
        return $this->current->valid();
    }

    public function next()
    {
        $this->current->next();
        if (!$this->current->valid()) {
            $this->findNextIterator();
        }
        $this->key++;
    }

    public function key()
    {
        return $this->key;
    }

    /**
     * @inheritdoc
     */
    public function getInnerIterator()
    {
        return $this->iterator;
    }
}