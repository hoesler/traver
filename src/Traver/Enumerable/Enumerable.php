<?php

namespace Traver\Enumerable;


use PhpOption\Option;
use Traver\Exception\NoSuchElementException;
use Traver\Exception\UnsupportedOperationException;
use Traversable;

interface Enumerable extends \Traversable, \Countable
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
     * Builds a new collection by applying a function to all elements of this traversable collection
     * and using the elements of the resulting collections.
     * @param callable $mappingFunction A function which maps each element of this collection to an array or a Traversable.
     * @return Enumerable
     */
    public function flatMap(callable $mappingFunction);

    /**
     * Applies a binary operator to a start value and all elements of this traversable or iterator, going left to right.
     * @param mixed $initialValue
     * @param callable $binaryFunction
     * @return mixed
     */
    public function foldLeft($initialValue, callable $binaryFunction);

    /**
     * Applies a binary operator to a start value and all elements of this traversable or iterator, going right to left.
     * @param mixed $initialValue
     * @param callable $binaryFunction
     * @return mixed
     */
    public function foldRight($initialValue, callable $binaryFunction);

    /**
     * Tests whether a predicate holds for all elements of this traversable collection.
     * @param callable $predicate
     * @return bool
     */
    public function forall(callable $predicate);

    /**
     * Partitions this traversable collection into a map of traversable collections according to some discriminator function.
     * <p>Note: this method is not re-implemented by views.
     * This means when applied to a view it will always force the view and return a new traversable collection.</p>
     * @param callable $keyFunction
     * @return Map
     */
    public function groupBy(callable $keyFunction);

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
     * @return Traversable
     */
    public function asTraversable();
}