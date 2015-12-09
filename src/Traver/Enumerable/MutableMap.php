<?php


namespace Traver\Enumerable;


class MutableMap implements \IteratorAggregate, Collection
{
    use EnumerableLike;
    use ForwardingArrayAccess;

    /**
     * @var \ArrayObject
     */
    private $delegate;

    /**
     * @param array $input
     */
    public function __construct($input = [])
    {
        $this->delegate = new \ArrayObject($input);
    }

    /**
     * @inheritDoc
     */
    public function asTraversable()
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function builder()
    {
        return new MutableMapBuilder();
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this->delegate()->getIterator();
    }

    /**
     * @inheritDoc
     */
    function delegate()
    {
        return $this->delegate;
    }

    public function isVectorLike()
    {
        return false;
    }
}

class MutableMapBuilder implements Builder
{
    use FromArrayBuilder;

    /**
     * @return Enumerable
     */
    public function build()
    {
        return new MutableMap($this->array);
    }
}