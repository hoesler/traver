<?php
namespace Traver\Collection\View;

use Traver\Collection\Enumerable;
use Traver\Collection\EnumerableView;
use Traver\Collection\EnumerableViewLike;
use Traver\Iterator\ReindexingIterator;
use Traver\Iterator\TransformingIterator;

/**
 * Class Transformed
 * @package Traver\Collection
 * @codeCoverageIgnore
 * @internal
 */
class Transformed implements \IteratorAggregate, Enumerable
{
    use EnumerableView;

    /**
     * @var callable
     */
    private $mappingFunction;
    private $delegate;

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
        $iterator = new TransformingIterator($this->delegate->getIterator(), $this->mappingFunction);
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