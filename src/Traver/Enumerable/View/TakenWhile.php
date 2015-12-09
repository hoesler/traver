<?php
namespace Traver\Enumerable\View;

use Traver\Enumerable\Enumerable;
use Traver\Enumerable\EnumerableView;
use Traver\Enumerable\EnumerableViewLike;
use Traver\Iterator\CallbackLimitIterator;
use Traver\Iterator\ReindexingIterator;

/**
 * Class TakenWhile
 * @package Traver\Enumerable
 * @codeCoverageIgnore
 * @internal
 */
class TakenWhile implements \IteratorAggregate, Enumerable
{
    use EnumerableView;

    /**
     * @var callable
     */
    private $predicate;

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
        $iterator = new CallbackLimitIterator($this->delegate->getIterator(), $this->predicate);
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