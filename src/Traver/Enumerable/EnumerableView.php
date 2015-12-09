<?php


namespace Traver\Enumerable;


trait EnumerableView
{
    use EnumerableViewLike;

    public function isVectorLike()
    {
        return $this->delegate()->isVectorLike();
    }

    /**
     * @return EnumerableViewLike
     */
    protected abstract function delegate();

    /**
     * @codeCoverageIgnore
     */
    final protected function builder()
    {
        return $this->delegate()->builder();
    }
}