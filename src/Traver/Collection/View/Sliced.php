<?php
namespace Traver\Collection\View;

use LimitIterator;
use Traver\Collection\Enumerable;
use Traver\Collection\EnumerableView;
use Traver\Collection\EnumerableViewLike;
use Traver\Iterator\ReindexingIterator;

/**
 * Class Sliced
 * @package Traver\Collection
 * @codeCoverageIgnore
 * @internal
 */
class Sliced implements \IteratorAggregate, Enumerable
{
    use EnumerableView;

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
     * @param EnumerableViewLike $delegate
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