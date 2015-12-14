<?php


namespace Traver\Collection;


class PipeableView implements \IteratorAggregate, Pipeable
{
    use PipeableViewLike;

    /**
     * @var Pipeable
     */
    private $delegate;

    /**
     * PipeableView constructor.
     * @param Pipeable $delegate
     */
    public function __construct($delegate)
    {
        $this->delegate = $delegate;
    }

    function getIterator()
    {
        return $this->delegate()->getIterator();
    }

    protected function delegate()
    {
        return $this->delegate;
    }

    protected function asPipeable()
    {
        return $this;
    }
}