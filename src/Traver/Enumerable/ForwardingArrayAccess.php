<?php


namespace Traver\Enumerable;


trait ForwardingArrayAccess
{
    /**
     * @return \ArrayAccess
     */
    abstract function delegate();

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->delegate()->offsetExists($offset);
    }

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