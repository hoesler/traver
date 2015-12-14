<?php
namespace Traver\Collection\View;

use Traver\Collection\Pipeable;
use Traver\Collection\PipeableViewLike;
use Traver\Iterator\CallbackLimitIterator;
use Traver\Iterator\ReindexingIterator;

/**
 * Class TakenWhile
 * @package Traver\Collection
 * @codeCoverageIgnore
 * @internal
 */
class TakenWhile implements \IteratorAggregate, Pipeable
{
    use PipeableViewLike;

    /**
     * @var callable
     */
    private $predicate;

    /**
     * Dropped constructor.
     * @param PipeableViewLike $delegate
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