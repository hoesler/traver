<?php
namespace Traver\Enumerable\View;

use Traver\Enumerable\Enumerable;
use Traver\Enumerable\EnumerableView;
use Traver\Enumerable\EnumerableViewLike;
use Traver\Iterator\ConcatIterator;
use Traver\Iterator\MappingIterator;
use Traver\Iterator\ReindexingIterator;

/**
 * Class FlatMapped
 * @package Traver\Enumerable
 * @codeCoverageIgnore
 * @internal
 */
class FlatMapped implements \IteratorAggregate, Enumerable
{
    use EnumerableView;

    /**
     * @var callable
     */
    private $mappingFunction;

    /**
     * MapView constructor.
     * @param EnumerableViewLike $delegate
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