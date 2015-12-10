<?php


namespace Traver\Collection;


trait PipeableView
{
    use PipeableViewLike;

    public function isVectorLike()
    {
        return $this->delegate()->isVectorLike();
    }

    /**
     * @return PipeableViewLike
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