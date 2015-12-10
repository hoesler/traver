<?php
namespace Traver\Collection\View;

use Traver\Collection\Enumerable;
use Traver\Collection\EnumerableView;
use Traver\Collection\EnumerableViewLike;
use Traver\Iterator\CallbackOffsetIterator;
use Traver\Iterator\ReindexingIterator;

/**
 * Class DroppedWhile
 * @package Traver\Collection
 * @codeCoverageIgnore
 * @internal
 */
class DroppedWhile implements \IteratorAggregate, Enumerable
{
    use EnumerableView;

    /**
     * @var callable
     */
    private $predicate;

    /**
     * @var EnumerableViewLike
     */
    private $delegate;

    /**
     * Dropped constructor.
     * @param EnumerableViewLike $delegate
     * @param callable $predicate
     */
    public function __construct($delegate, $predicate)
    {
        $this->delegate = $delegate;
        $this->predicate = $predicate;
    }

    public function getIterator()
    {
        $iterator = new CallbackOffsetIterator($this->delegate->getIterator(), $this->predicate);
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