<?php


namespace Traver\Enumerable;


use ArrayObject;
use Iterator;

class ImmutableMap implements \IteratorAggregate, Collection
{
    use EnumerableViewLike;
    use ImmutableArrayAccess;

    /**
     * @var ArrayObject
     */
    private $delegate;

    /**
     * ImmutableVector constructor.
     * @param ArrayObject $delegate
     */
    private function __construct($delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @inheritDoc
     */
    function __clone()
    {
    }

    /**
     * @return Iterator
     */
    function getIterator()
    {
        return $this->delegate->getIterator();
    }

    /**
     * @return \Traversable|\ArrayAccess|\Countable
     */
    function delegate()
    {
        return $this->delegate;
    }

    public static function fromArray($array)
    {
        return new self(new \ArrayObject($array));
    }

    public function builder()
    {
        return self::newBuilder();
    }

    public static function newBuilder()
    {
        return new ImmutableMapBuilder();
    }
}

class ImmutableMapBuilder implements Builder
{
    use FromArrayBuilder;

    /**
     * @inheritDoc
     */
    public function build()
    {
        return ImmutableMap::fromArray($this->array);
    }
}