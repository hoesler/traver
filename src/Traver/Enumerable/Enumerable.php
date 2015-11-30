<?php

namespace Traver\Enumerable;


use Iterator;
use PhpOption\Option;
use Traver\Exception\NoSuchElementException;
use Traver\Exception\UnsupportedOperationException;

interface Enumerable extends \IteratorAggregate, \Countable
{
    /**
     * Creates a new traversable collection by applying a function to all elements of this traversable collection.
     * @param callable $mappingFunction the function to apply to each element. Arguments are: value, key.
     * @return Enumerable A new traversable collection resulting from applying the given mappingFunction to each element
     * of this traversable collection and collecting the results.
     */
    public function map(callable $mappingFunction);

    /**
     * Selects the first element of this traversable collection.
     * @return mixed
     * @throws NoSuchElementException if the traversable collection is empty.
     */
    public function head();

    /**
     * Selects all elements except the first.
     * The returned traversable preserves the keys.
     * @return Enumerable
     * @throws UnsupportedOperationException if the traversable collection is empty.
     */
    public function tail();

    /**
     * Aggregates the results of applying an operator to subsequent elements.
     * @param callable $binaryFunction called with 3 arguments during iteration: next value, current aggregate, key of value.
     * @param mixed $initialValue
     * @return mixed
     */
    public function aggregate(callable $binaryFunction, $initialValue = null);

    /**
     * Counts the number of elements in the traversable or iterator which satisfy a predicate.
     * @param callable $predicate
     * @return int
     */
    public function countWhich(callable $predicate);

    /**
     * Selects all elements except first n ones.
     * @param int $n
     * @return Enumerable
     */
    public function drop($n);

    /**
     * Drops longest prefix of elements that satisfy a predicate.
     * @param callable $predicate
     * @return Enumerable
     */
    public function dropWhile(callable $predicate);

    /**
     * Tests whether a predicate holds for some of the elements of this traversable collection.
     * @param callable $predicate
     * @return bool
     */
    public function exists(callable $predicate);

    /**
     * Selects all elements of this traversable collection which satisfy a predicate.
     * @param callable $predicate
     * @return Enumerable
     */
    public function filter(callable $predicate);

    /**
     * Selects all elements of this traversable collection which do not satisfy a predicate.
     * @param callable $predicate
     * @return Enumerable
     */
    public function filterNot(callable $predicate);

    /**
     * @param callable $predicate
     * @return Option
     */
    public function find(callable $predicate);

    /**
     * Selects an interval of elements.
     * @param int $from
     * @param int $until
     * @return Enumerable
     */
    public function slice($from, $until);

    /**
     * @param callable $f
     */
    public function each(callable $f);

    /**
     * Maps the values to their keys.
     * @return Enumerable
     */
    public function keys();

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * Transforms this Enumerable to an array.
     * @param bool $preserveKeys
     * @return array
     */
    public function toArray($preserveKeys = true);

    /**
     * @return Iterator
     */
    public function getIterator();
}