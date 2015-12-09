<?php

namespace Traver;

use ArrayObject;
use Traver\Callback\OperatorCallbacks;
use Traver\Enumerable\Enumerable;
use Traver\Enumerable\ImmutableMap;
use Traver\Enumerable\ImmutableVector;
use Traversable;

if (!function_exists('Traver\view')) {
    /**
     * @param array|Traversable $collection
     * @return Enumerable
     */
    function view($collection)
    {
        if (is_a($collection, ImmutableMap::class) || is_a($collection, ImmutableVector::class)) {
            return $collection;
        } elseif (is_array($collection)) {
            return ImmutableMap::fromArray($collection);
        } else {
            return ImmutableMap::fromArray(new ArrayObject(iterator_to_array($collection)));
        }
    }
}

if (!function_exists('Traver\in')) {
    /**
     * @param array|Traversable $collection
     * @return Enumerable
     */
    function in($collection)
    {
        return view($collection);
    }
}

if (!function_exists('Traver\head')) {
    /**
     * @param array|Traversable $collection
     * @return mixed
     */
    function head($collection)
    {
        return view($collection)->head();
    }
}

if (!function_exists('Traver\tail')) {
    /**
     * @param array|Traversable $collection
     * @return array
     */
    function tail($collection)
    {
        return view($collection)->tail()->toArray();
    }
}

if (!function_exists('Traver\map')) {
    /**
     * @param array|Traversable $collection
     * @param callable $mappingFunction
     * @return array
     */
    function map($collection, callable $mappingFunction)
    {
        return view($collection)->map($mappingFunction)->toArray();
    }
}

if (!function_exists('Traver\reduce')) {
    /**
     * @param array|Traversable $collection
     * @param callback $binaryFunction
     * @param null $initial
     * @return array
     */
    function reduce($collection, $binaryFunction, $initial = null)
    {
        if (func_num_args() == 1) {
            return view($collection)->reduce($binaryFunction);
        } else {
            return view($collection)->reduce($binaryFunction, $initial);
        }
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