<?php


namespace Traver\Enumerable;


trait EnumerableView
{
    use EnumerableViewLike;

    /**
     * @codeCoverageIgnore
     */
    final protected function builder()
    {
        return $this->delegate()->builder();
    }

    /**
     * @return EnumerableViewLike
     */
    protected abstract function delegate();
}