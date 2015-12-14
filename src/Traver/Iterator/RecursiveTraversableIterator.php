<?php


namespace Traver\Iterator;


use Iterator;
use RecursiveIterator;
use Traversable;

class RecursiveTraversableIterator extends ForwardingIterator implements \RecursiveIterator
{
    /**
     * @var Iterator
     */
    private $iterator;
    /**
     * @var int
     */
    private $level;

    /**
     * RecursiveTraversableIterator constructor.
     * @param Traversable $traversable
     * @param int $level
     */
    public function __construct($traversable, $level)
    {
        if ($traversable instanceof Iterator) {
            $this->iterator = $traversable;
        } elseif ($traversable instanceof \IteratorAggregate) {
            $this->iterator = $traversable->getIterator();
        } else {
            throw new \InvalidArgumentException();
        }
        $this->level = $level;
    }

    public function hasChildren()
    {
        return $this->level != 0 && ($this->current() instanceof Traversable || is_array($this->current()));
    }

    public function getChildren()
    {
        $traversable = $this->current();
        if ($this->current() instanceof Traversable) {
            return new RecursiveTraversableIterator($traversable, $this->level - 1);
        } elseif (is_array($this->current())) {
            return new RecursiveTraversableIterator(new \ArrayIterator($this->current()), $this->level - 1);
        } else {
            throw new \RuntimeException();
        }
    }

    public function getInnerIterator()
    {
        return $this->iterator;
    }
}