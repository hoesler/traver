<?php


namespace Traver\Collection;


use Iterator;
use SplFixedArray;

/**
 * Class ImmutableVector
 *
 * This class implements {@link \Traver\Collection\Collection} using an SplFixedArray.
 * @package Traver\Collection
 */
class ImmutableVector implements \IteratorAggregate, Vector
{
    use VectorLike;
    use ImmutableArrayAccess;

    /**
     * @var SplFixedArray
     */
    private $delegate;

    /**
     * ImmutableVector constructor.
     * @param SplFixedArray $delegate
     */
    private function __construct($delegate)
    {
        $this->delegate = $delegate;
    }

    public static function fromArray($array, $save_indexes = true)
    {
        return new self(\SplFixedArray::fromArray($array, $save_indexes));
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
        return self::newBuilder();
    }

    public static function newBuilder()
    {
        return new ImmutableVectorBuilder();
    }

    public function isVectorLike()
    {
        return true;
    }

    private function __clone()
    {
    }
}

class ImmutableVectorBuilder implements Builder
{
    use FromArrayBuilder;

    public function build()
    {
        return ImmutableVector::fromArray($this->array);
    }

    /**
     * @inheritDoc
     */
    public function add($element, $key = null)
    {
        array_push($this->array, $element);
    }
}