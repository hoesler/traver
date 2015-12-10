<?php


namespace Traver\Collection;


use ArrayObject;
use Iterator;

class ImmutableMap implements \IteratorAggregate, Collection
{
    use PipeableViewLike;
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
     * @param array $array
     * @return ImmutableMap
     */
    public static function fromArray(array $array)
    {
        return new self(new \ArrayObject($array));
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

    public function builder()
    {
        return self::newBuilder();
    }

    public static function newBuilder()
    {
        return new ImmutableMapBuilder();
    }

    public function isVectorLike()
    {
        return false;
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