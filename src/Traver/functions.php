<?php

namespace Traver;

use Traver\Callback\OperatorCallbacks;
use Traver\Collection\ImmutableMap;
use Traver\Collection\ImmutableVector;
use Traver\Collection\MutableMap;
use Traver\Collection\MutableVector;
use Traver\Collection\Range;
use Traversable;

if (!function_exists('Traver\map')) {
    /**
     * Creates a new {@link ImmutableMap} from the given elements.
     * @param array|Traversable $elements
     * @return ImmutableMap
     * @see in alias
     */
    function map($elements = [])
    {
        return ImmutableMap::copyOf($elements);
    }
}

if (!function_exists('Traver\mutable_map')) {
    /**
     * Creates a new {@link MutableMap} from the given elements.
     * @param array|Traversable $elements
     * @return ImmutableMap
     * @see in alias
     */
    function mutable_map($elements = [])
    {
        return new MutableMap($elements);
    }
}

if (!function_exists('Traver\vector')) {
    /**
     * Creates a new {@link ImmutableVector} from the given elements.
     * @param mixed ...$elements
     * @return ImmutableVector
     */
    function vector($elements = [])
    {
        return ImmutableVector::copyOf($elements);
    }
}

if (!function_exists('Traver\mutable_vector')) {
    /**
     * Creates a new {@link MutableVector} from the given elements.
     * @param mixed ...$elements
     * @return ImmutableVector
     */
    function mutable_vector($elements = [])
    {
        return new MutableVector($elements);
    }
}

if (!function_exists('Traver\in')) {
    /**
     * Creates a new {@link ImmutableMap} from the given elements.
     * @param array|Traversable $collection
     * @return ImmutableMap
     * @see map alias
     */
    function in($collection)
    {
        return ImmutableMap::copyOf($collection);
    }
}

if (!function_exists('Traver\range')) {
    /**
     * Creates a new {@link \Traver\Collection\Range Range} from the given elements.
     * @param $start
     * @param $end
     * @param bool $exclusive
     * @return Collection\Range
     */
    function range($start, $end, $exclusive = false)
    {
        return new Range($start, $end, $exclusive);
    }
}

if (!function_exists('Traver\op')) {
    /**
     * @param string $operator
     * @return callable
     */
    function op($operator)
    {
        return OperatorCallbacks::forOperator($operator)
            ->getOrThrow(new \RuntimeException("No function defined for operator $operator"));
    }
}