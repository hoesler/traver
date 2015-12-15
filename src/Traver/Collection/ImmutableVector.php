<?php


namespace Traver\Collection;


use SplFixedArray;
use Traversable;

/**
 * Class ImmutableVector
 *
 * This class implements {@link \Traver\Collection\Collection} using an SplFixedArray.
 * @package Traver\Collection
 */
class ImmutableVector implements \IteratorAggregate, Vector
{
    use VectorLike;
    use ForwardingArrayAccess;
    use ImmutableCollection {
        ImmutableCollection::offsetSet insteadof ForwardingArrayAccess;
        ImmutableCollection::offsetUnset insteadof ForwardingArrayAccess;
        ImmutableCollection::count insteadof VectorLike;
    }

    /**
     * @var SplFixedArray
     */
    private $delegate;

    /**
     * ImmutableVector constructor.
     * @param SplFixedArray $delegate
     * @codeCoverageIgnore
     */
    private function __construct($delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * Create a new ImmutableVector from the given elements.
     * @param ...$elements
     * @return ImmutableVector
     */
    public static function of(...$elements)
    {
        return new self(\SplFixedArray::fromArray($elements));
    }

    /**
     * Create a new ImmutableVector from the given traversable.
     * @param array|Traversable $traversable
     * @param bool $preserveKeys
     * @return ImmutableVector
     */
    public static function copyOf($traversable, $preserveKeys = true)
    {
        if (is_array($traversable)) {
            return new self(\SplFixedArray::fromArray($traversable, $preserveKeys));
        } else {
            return new self(\SplFixedArray::fromArray(iterator_to_array($traversable), $preserveKeys));
        }
    }

    public function getIterator()
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

    /**
     * @codeCoverageIgnore
     */
    protected function delegate()
    {
        return $this->delegate;
    }

    protected function getSize()
    {
        return $this->delegate->count();
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
        return ImmutableVector::copyOf($this->array);
    }

    public function add($element, $key = null)
    {
        array_push($this->array, $element);
    }
}