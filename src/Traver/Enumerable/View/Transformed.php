<?php
namespace Traver\Enumerable\View;

use Traver\Enumerable\Enumerable;
use Traver\Enumerable\EnumerableView;
use Traver\Enumerable\EnumerableViewLike;
use Traver\Iterator\ReindexingIterator;
use Traver\Iterator\TransformingIterator;

/**
 * Class Transformed
 * @package Traver\Enumerable
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