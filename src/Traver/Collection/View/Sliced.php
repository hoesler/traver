<?php
namespace Traver\Collection\View;

use LimitIterator;
use Traver\Collection\Pipeable;
use Traver\Collection\PipeableView;
use Traver\Collection\PipeableViewLike;
use Traver\Iterator\ReindexingIterator;

/**
 * Class Sliced
 * @package Traver\Collection
 * @codeCoverageIgnore
 * @internal
 */
class Sliced implements \IteratorAggregate, Pipeable
{
    use PipeableView;

    /**
     * @var
     */
    private $from;

    /**
     * @var
     */
    private $until;

    /**
     * MapView constructor.
     * @param PipeableViewLike $delegate
     * @param $from
     * @param $until
     */
    public function __construct($delegate, $from = 0, $until = PHP_INT_MAX)
    {
        $this->delegate = $delegate;
        $this->from = $from;
        $this->until = $until;
    }

    public function getIterator()
    {
        $offset = $this->from;
        $count = max($this->until - $this->from, 0);
        if ($count == 0) {
            return new \EmptyIterator();
        } else {
            $iterator = new LimitIterator($this->delegate->getIterator(), $offset, $count);
            if ($this->delegate->isVectorLike()) {
                $iterator = new ReindexingIterator($iterator);
            }
            return $iterator;
        }
    }

    protected function delegate()
    {
        return $this->delegate;
    }
}