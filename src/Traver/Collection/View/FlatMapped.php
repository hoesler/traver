<?php
namespace Traver\Collection\View;

use Traver\Collection\Pipeable;
use Traver\Collection\PipeableViewLike;
use Traver\Iterator\ConcatIterator;
use Traver\Iterator\MappingIterator;
use Traver\Iterator\ReindexingIterator;

/**
 * Class FlatMapped
 * @package Traver\Collection
 * @codeCoverageIgnore
 * @internal
 */
class FlatMapped implements \IteratorAggregate, Pipeable
{
    use PipeableViewLike;

    /**
     * @var callable
     */
    private $mappingFunction;

    /**
     * MapView constructor.
     * @param PipeableViewLike $delegate
     * @param callable $mappingFunction
     */
    public function __construct($delegate, $mappingFunction)
    {
        $this->mappingFunction = $mappingFunction;
        $this->delegate = $delegate;
    }

    public function getIterator()
    {
        $iterator = new ConcatIterator(
            new MappingIterator(
                new MappingIterator($this->delegate->getIterator(), $this->mappingFunction),
                function ($element) {
                    if ($element instanceof \Traversable) {
                        return new \IteratorIterator($element);
                    } elseif (is_array($element)) {
                        return new \ArrayIterator($element);
                    } else {
                        return new \ArrayIterator([$element]); // TODO Implement a singleton iterator
                    }
                })
        );
        if ($this->delegate->isVectorLike()) {
            $iterator = new ReindexingIterator($iterator);
        }
        return $iterator;
    }

    protected function delegate()
    {
        return $this->delegate;
    }
}