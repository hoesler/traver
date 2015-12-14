<?php


namespace Traver\Collection\View;


use Traver\Collection\Pipeable;
use Traver\Collection\PipeableView;
use Traver\Collection\PipeableViewLike;
use Traver\Iterator\RecursiveTraversableIterator;
use Traver\Iterator\ReindexingIterator;

class Flattened implements \IteratorAggregate, Pipeable
{
    use PipeableView;

    /**
     * @var PipeableViewLike
     */
    private $delegate;
    /**
     * @var int
     */
    private $level;

    /**
     * Flattened constructor.
     * @param PipeableViewLike $delegate
     * @param int $level
     */
    public function __construct($delegate, $level)
    {
        $this->delegate = $delegate;
        $this->level = $level;
    }

    function getIterator()
    {
        return new ReindexingIterator(
            new \RecursiveIteratorIterator(
                new RecursiveTraversableIterator($this->delegate()->asTraversable(), $this->level)));
    }

    protected function delegate()
    {
        return $this->delegate;
    }
}