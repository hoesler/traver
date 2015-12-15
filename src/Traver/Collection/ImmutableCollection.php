<?php


namespace Traver\Collection;


trait ImmutableCollection
{
    use ImmutableArrayAccess;

    private $count;

    final public function count()
    {
        if (!isset($this->count)) {
            $this->count = $this->getSize();
        }
        return $this->count;
    }

    /**
     * @return int
     */
    protected abstract function getSize();
}