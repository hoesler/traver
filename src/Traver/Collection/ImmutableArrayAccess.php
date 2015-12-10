<?php


namespace Traver\Collection;


use Traver\Exception\UnsupportedOperationException;

trait ImmutableArrayAccess
{
    use ForwardingArrayAccess;

    final public function offsetSet(/** @noinspection PhpUnusedParameterInspection */
        $offset, $value)
    {
        throw new UnsupportedOperationException("immutable");
    }

    final public function offsetUnset(/** @noinspection PhpUnusedParameterInspection */
        $offset)
    {
        throw new UnsupportedOperationException("immutable");
    }
}