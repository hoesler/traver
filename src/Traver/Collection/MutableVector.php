<?php


namespace Traver\Collection;


use Iterator;
use SplFixedArray;

class MutableVector implements \IteratorAggregate, Vector
{
    use VectorLike;
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
    function getIterator()
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

    /**
     * @return \Traversable|\ArrayAccess|\Countable
     */
    protected function delegate()
    {
        return $this->delegate;
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