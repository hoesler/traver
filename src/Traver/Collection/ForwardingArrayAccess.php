<?php


namespace Traver\Collection;


trait ForwardingArrayAccess
{
    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->delegate()->offsetExists($offset);
    }

    /**
     * @return \ArrayAccess
     */
    abstract protected function delegate();

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->delegate()->offsetGet($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->delegate()->offsetSet($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $this->delegate()->offsetUnset($offset);
    }
}