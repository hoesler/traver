<?php

namespace Traver\Enumerable;


use PhpOption\Option;
use Traver\Exception\NoSuchElementException;
use Traver\Exception\UnsupportedOperationException;
use Traversable;

interface Enumerable extends \Traversable, \Countable
{
    /**
     * Creates a new Enumerable by applying a function to all elements of {@link asTraversable()}.
     * @param callable $mappingFunction the function to apply to each element. Arguments are: value, key.
     * @param bool $preserveKeys
     * @return Enumerable A new traversable collection resulting from applying the given mappingFunction to each element
     * of this traversable collection and collecting the results.
     */
    public function map(callable $mappingFunction, $preserveKeys = true);

    /**
     * Selects the first element of this collection.
     * @return mixed
     * @throws NoSuchElementException if the traversable collection is empty.
     * @see tail
     */
    public function head();

    /**
     * Selects all elements in the enumerable except the first.
     * The returned traversable preserves the keys.
     * @return Enumerable
     * @throws UnsupportedOperationException if the traversable collection is empty.
     * @see head
     */
    public function tail();

    /**
     * Counts the number of elements in the enumerable which satisfy a predicate.
     * @param callable $predicate
     * @return int
     */
    public function countWhich(callable $predicate);

    /**
     * Selects all elements except first n ones.
     * @param int $n
     * @return Enumerable
     * @see slice
     */
    public function drop($n);

    /**
     * Drops longest prefix of elements that satisfy a predicate.
     * @param callable $predicate
     * @return Enumerable
     */
    public function dropWhile(callable $predicate);

    /**
     * Selects the first n elements.
     * @param int $n
     * @return Enumerable
     * @see slice
     */
    public function take($n);

    /**
     * Selects the longest prefix of elements that satisfy a predicate.
     * @param callable $predicate
     * @return Enumerable
     */
    public function takeWhile(callable $predicate);

    /**
     * Tests whether a predicate holds for some of the elements of the enumerable.
     * @param callable $predicate
     * @return bool
     */
    public function exists(callable $predicate);

    /**
     * Selects all elements of the enumerable which satisfy a predicate.
     * @param callable $predicate
     * @return Enumerable
     * @see reject
     */
    public function select(callable $predicate);

    /**
     * Selects all elements of the enumerable which do not satisfy a predicate.
     * @param callable $predicate
     * @return Enumerable
     * @see select
     */
    public function reject(callable $predicate);

    /**
     * @param callable $predicate
     * @return Option
     */
    public function find(callable $predicate);

    /**
     * Builds a new collection by applying a function to all elements of the enumerable.
     * and using the elements of the resulting collections.
     * @param callable $mappingFunction A function which maps each element of this collection to an array or a Traversable.
     * @return Enumerable
     */
    public function flatMap(callable $mappingFunction);

    /**
     * Tests whether a predicate holds for all elements of the enumerable.
     * @param callable $predicate
     * @return bool
     * @see any
     */
    public function all(callable $predicate);

    /**
     * Tests whether a predicate holds for any element of the enumerable.
     * @param callable $predicate
     * @return bool
     * @see all
     */
    public function any(callable $predicate);

    /**
     * Partitions the enumerable into a map of traversable collections according to some discriminator function.
     * <p>Note: this method is not re-implemented by views.
     * This means when applied to a view it will always force the view and return a new traversable collection.</p>
     * @param callable $keyFunction
     * @return Map
     */
    public function groupBy(callable $keyFunction);

    /**
     * Concatenates the strval of all elements separated by the given separator.
     * @param $separator
     * @return mixed
     * @see reduce
     */
    public function join($separator = '');

    /**
     * Applies the binary function to all elements of the enumerable with initial as the first value, if given.
     * Throws a {@link NoSuchElementException} If the traversable is empty and initial is not given.
     * @param callable $binaryFunction
     * @param mixed $initial
     * @return mixed
     * @throws NoSuchElementException
     */
    public function reduce(callable $binaryFunction, $initial = null);

    /**
     * Applies the binary function to all elements of the enumerable with initial as the first value, if given.
     * Returns the result as {@link Some}.
     * If the traversable is empty and initial is not given, return {@link None}.
     * @param callable $binaryFunction
     * @param mixed $initial
     * @return Option
     */
    public function reduceOption(callable $binaryFunction, $initial = null);
    
    /**
     * Selects the elements in the interval [from, until).
     * @param int $from
     * @param int $until
     * @return Enumerable
     * @see take
     * @see drop
     */
    public function slice($from, $until);

    /**
     * Apply function f to all elements in this enumerable.
     * @param callable $f
     */
    public function each(callable $f);

    /**
     * Returns the keys for all elements.
     * @return Enumerable
     */
    public function keys();

    /**
     * Tests if this collection is empty.
     * @return bool
     */
    public function isEmpty();

    /**
     * Transforms this enumerable into an array.
     * @param bool $preserveKeys indicates if the keys should be preserved or if values should be re-indexed.
     * @return array
     */
    public function toArray($preserveKeys = true);

    /**
     * Returns this Enumerable as a traversable collection.
     * <p>This function is primarily a workaround to the inability of traits to implement interfaces.
     * Because all classes implementing Enumerable should also implement {@link Traversable},
     * most implementation simply return <code>$this</code>.</p>
     * @return Traversable
     */
    public function asTraversable();
}