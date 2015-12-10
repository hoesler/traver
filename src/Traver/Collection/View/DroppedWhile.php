<?php
namespace Traver\Collection\View;

use Traver\Collection\Pipeable;
use Traver\Collection\PipeableView;
use Traver\Collection\PipeableViewLike;
use Traver\Iterator\CallbackOffsetIterator;
use Traver\Iterator\ReindexingIterator;

/**
 * Class DroppedWhile
 * @package Traver\Collection
 * @codeCoverageIgnore
 * @internal
 */
class DroppedWhile implements \IteratorAggregate, Pipeable
{
    use PipeableView;

    /**
     * @var callable
     */
    private $predicate;

    /**
     * @var PipeableViewLike
     */
    private $delegate;

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