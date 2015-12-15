<?php


namespace Traver\Collection;


use ArrayObject;
use Traversable;

class ImmutableMap implements \IteratorAggregate, Map
{
    use MapLike;
    use ForwardingArrayAccess;
    use ImmutableCollection {
        ImmutableCollection::offsetSet insteadof ForwardingArrayAccess;
        ImmutableCollection::offsetUnset insteadof ForwardingArrayAccess;
        ImmutableCollection::count insteadof MapLike;
    }

    /**
     * @var ArrayObject
     */
    private $delegate;

    /**
     * ImmutableVector constructor.
     * @param ArrayObject $delegate
     * @codeCoverageIgnore
     */
    private function __construct($delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * Create a new ImmutableMap from the given elements.
     * @param ...$elements
     * @return ImmutableMap
     */
    public static function of(...$elements)
    {
        return new self(new ArrayObject($elements));
    }

    /**
     * @param array|Traversable $traversable
     * @return ImmutableMap
     */
    public static function copyOf($traversable)
    {
        return new self(new ArrayObject($traversable));
    }

    public function getIterator()
    {
        return $this->delegate->getIterator();
    }

    public function builder()
    {
        return self::newBuilder();
    }

    public static function newBuilder()
    {
        return new ImmutableMapBuilder();
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

    /**
     * @codeCoverageIgnore
     */
    private function __clone()
    {
    }
}

/**
 * Class ImmutableMapBuilder
 * @package Traver\Collection
 */
class ImmutableMapBuilder implements Builder
{
    use FromArrayBuilder;

    public function build()
    {
        return ImmutableMap::copyOf($this->array);
    }
}