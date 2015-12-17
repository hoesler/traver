<?php


namespace Traver\Iterator;


class RangeIterator implements \Iterator
{
    private $start;
    private $end;
    private $exclusive;
    private $current;
    private $index;

    /**
     * RangeIterator constructor.
     * @param mixed $start
     * @param mixed $end
     * @param bool $exclusive
     */
    public function __construct($start, $end, $exclusive = false)
    {
        $this->start = $start;
        $this->end = $end;
        $this->exclusive = $exclusive;
        $this->current = $this->start;
        $this->index = 0;
    }

    public function next()
    {
        ++$this->index;
        ++$this->current;
    }

    public function key()
    {
        return $this->index;
    }

    public function valid()
    {
        if ($this->current() < $this->start) {
            return false;
        }

        if ($this->exclusive && $this->current() >= $this->end) {
            return false;
        }

        if (!$this->exclusive && $this->current() > $this->end) {
            return false;
        }

        return true;
    }

    public function current()
    {
        return $this->current;
    }

    public function rewind()
    {
        $this->current = $this->start;
        $this->index = 0;
    }
}