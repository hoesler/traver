<?php


namespace Traver\Iterator;


use Iterator;

class ReindexingIterator extends ForwardingIterator
{
    /**
     * @var int
     */
    private $index;
    /**
     * @var Iterator
     */
    private $delegate;

    /**
     * ReindexingIterator constructor.
     * @param Iterator $delegate
     */
    public function __construct($delegate)
    {
        $this->delegate = $delegate;
    }


    public function key()
    {
        return $this->index;
    }

    public function next()
    {
        parent::next();
        $this->index++;
    }

    public function rewind()
    {
        parent::rewind();
        $this->index = 0;
    }

    public function getInnerIterator()
    {
        return $this->delegate;
    }
}