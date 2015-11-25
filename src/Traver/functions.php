<?php

namespace Traver;

use ArrayIterator;
use Iterator;
use IteratorAggregate;
use Traver\Enumerable\Enumerable;
use Traver\Enumerable\IteratingEnumerable;
use Traversable;

if (!function_exists('Traver\traver')) {
    /**
     * @param array|Traversable $collection
     * @return Enumerable
     */
    function traver($collection)
    {
        if (is_array($collection)) {
            return new IteratingEnumerable(new ArrayIterator($collection));
        } elseif ($collection instanceof Iterator) {
            return new IteratingEnumerable($collection);
        } elseif ($collection instanceof IteratorAggregate) {
            return new IteratingEnumerable($collection->getIterator());
        } else {
            // TODO: do we ever get here?
            return new IteratingEnumerable(new ArrayIterator(iterator_to_array($collection)));
        }
    }
}

if (!function_exists('Traver\head')) {
    /**
     * @param array|Traversable $collection
     * @return mixed
     */
    function head($collection)
    {
        return traver($collection)->head();
    }
}

if (!function_exists('Traver\tail')) {
    /**
     * @param array|Traversable $collection
     * @return array
     */
    function tail($collection)
    {
        return traver($collection)->tail()->toArray();
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
        return traver($collection)->map($mappingFunction)->toArray();
    }
}