<?php

namespace Traver;

use ArrayObject;
use IteratorAggregate;
use Traver\Enumerable\Enumerable;
use Traver\Enumerable\EnumerableView;
use Traversable;

if (!function_exists('Traver\view')) {
    /**
     * @param array|Traversable $collection
     * @return Enumerable
     */
    function view($collection)
    {
        if (is_array($collection)) {
            return new EnumerableView(new ArrayObject($collection));
        } elseif ($collection instanceof IteratorAggregate) {
            return new EnumerableView($collection);
        } else {
            return new EnumerableView(new ArrayObject(iterator_to_array($collection)));
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