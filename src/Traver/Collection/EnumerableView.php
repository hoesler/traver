<?php


namespace Traver\Collection;


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
    abstract protected function delegate();

    /**
     * @codeCoverageIgnore
     */
    final protected function builder()
    {
        return $this->delegate()->builder();
    }
}