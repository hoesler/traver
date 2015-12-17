<?php


namespace Traver\Collection;


use Traver\Iterator\RangeIterator;

class Range implements \IteratorAggregate, Pipeable
{
    use PipeableLike;

    private $start;
    private $end;
    private $exclusive;

    /**
     * Range constructor.
     * @param mixed $start
     * @param mixed $end
     * @param bool $exclusive
     */
    public function __construct($start, $end, $exclusive = false)
    {
        $this->start = $start;
        $this->end = $end;
        $this->exclusive = $exclusive;
    }

    public function getIterator()
    {
        return new RangeIterator($this->start, $this->end, $this->exclusive);
    }

    public function builder()
    {
        return ImmutableVector::newBuilder();
    }

    // TODO: isVectorLike does not apply to ranges
    public function isVectorLike()
    {
        return true;
    }
}