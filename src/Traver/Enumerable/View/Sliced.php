<?php
namespace Traver\Enumerable\View;

use LimitIterator;
use Traver\Enumerable\Enumerable;
use Traver\Enumerable\EnumerableView;
use Traver\Enumerable\EnumerableViewLike;
use Traver\Iterator\ReindexingIterator;

/**
 * Class Sliced
 * @package Traver\Enumerable
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