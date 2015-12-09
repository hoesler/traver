<?php


namespace Traver\Iterator;


abstract class ForwardingIterator implements \OuterIterator
{
    public function current()
    {
        return $this->getInnerIterator()->current();
    }

    public function next()
    {
        $this->getInnerIterator()->next();
    }

    public function key()
    {
        return $this->getInnerIterator()->key();
    }

    public function valid()
    {
        return $this->getInnerIterator()->valid();
    }

    public function rewind()
    {
        $this->getInnerIterator()->rewind();
    }
}