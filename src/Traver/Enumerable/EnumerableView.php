<?php


namespace Traver\Enumerable;


class EnumerableView implements Enumerable
{
    use EnumerableViewLike;

    /**
     * EnumerableView constructor.
     * @param \IteratorAggregate $delegate
     */
    public function __construct($delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this->delegate->getIterator();
    }
}