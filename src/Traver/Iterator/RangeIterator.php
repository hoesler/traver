<?php


namespace Traver\Iterator;


use Traver\Callback\Comparators;

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
        $relation = call_user_func(Comparators::naturalComparator(), $this->current(), $this->end);
        if ($this->exclusive) {
            return $relation < 0;
        } else {
            return $relation <= 0;
        }
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