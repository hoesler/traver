<?php


namespace Traver\Collection;


use Iterator;
use SplFixedArray;

class MutableVector implements \IteratorAggregate, Collection
{
    use PipeableLike;
    use ForwardingArrayAccess;

    /**
     * @var SplFixedArray
     */
    private $delegate;

    /**
     * ImmutableVector constructor.
     * @param $array
     */
    public function __construct($array = [])
    {
        $this->delegate = SplFixedArray::fromArray($array);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        if ($this->delegate->getSize() == 0) {
            $this->delegate->setSize($offset + 1);
        }
        while ($offset >= $this->delegate->getSize()) {
            $this->delegate->setSize($this->delegate->getSize() << 1);
        }
        $this->delegate->offsetSet($offset, $value);
    }

    /**
     * @return Iterator
     */
    function asTraversable()
    {
        return $this->delegate;
    }

    /**
     * @return Iterator
     */
    function getIterator()
    {
        return $this->delegate;
    }

    /**
     * @return \Traversable|\ArrayAccess|\Countable
     */
    function delegate()
    {
        return $this->delegate;
    }

    public function builder()
    {
        return new MutableVectorBuilder();
    }

    public function isVectorLike()
    {
        return true;
    }
}

class MutableVectorBuilder implements Builder
{
    use FromArrayBuilder;

    /**
     * @return Pipeable
     */
    public function build()
    {
        return new MutableVector($this->array);
    }

    /**
     * @inheritDoc
     */
    public function add($element, $key = null)
    {
        array_push($this->array, $element);
    }
}