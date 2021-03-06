<?php

namespace Traver\Collection;


use Iterator;
use PhpOption\Option;
use Traver\Exception\NoSuchElementException;
use Traver\Exception\UnsupportedOperationException;
use Traversable;

/**
 * Interface Pipeable
 * @package Traver\Collection
 */
interface Pipeable extends \Traversable, \Countable
{
    /**
     * Creates a new Pipeable by applying a function to all elements of {@link asTraversable()}.
     * @param callable $mappingFunction the function to apply to each value. Arguments are: value.
     * @return Pipeable A new traversable collection resulting from applying the given mappingFunction to each element
     * of this traversable collection and collecting the results.
     */
    public function map(callable $mappingFunction);

    /**
     * Selects the first element of this collection.
     * @return mixed
     * @throws NoSuchElementException if the traversable collection is empty.
     * @see take
     */
    public function head();

    /**
     * Selects all elements in the pipeable except the first.
     * The returned traversable preserves the keys.
     * @return Pipeable
     * @throws UnsupportedOperationException if the traversable collection is empty.
     * @see drop
     */
    public function tail();

    /**
     * Counts the number of elements in the pipeable which satisfy a predicate.
     * @param callable $predicate
     * @return int
     */
    public function countWhich(callable $predicate);

    /**
     * Selects all elements except first n ones.
     * @param int $n
     * @return Pipeable
     * @see slice
     */
    public function drop($n);

    /**
     * Drops longest prefix of elements that satisfy a predicate.
     * @param callable $predicate
     * @return Pipeable
     */
    public function dropWhile(callable $predicate);

    /**
     * Selects the first n elements.
     * @param int $n
     * @return Pipeable
     * @see slice
     */
    public function take($n);

    /**
     * Selects the longest prefix of elements that satisfy a predicate.
     * @param callable $predicate
     * @return Pipeable
     */
    public function takeWhile(callable $predicate);

    /**
     * Selects all elements of the pipeable which satisfy a predicate.
     * @param callable $predicate
     * @return Pipeable
     * @see reject
     */
    public function select(callable $predicate);

    /**
     * Selects all elements of the pipeable which do not satisfy a predicate.
     * @param callable $predicate
     * @return Pipeable
     * @see select
     */
    public function reject(callable $predicate);

    /**
     * @param callable $predicate
     * @return Option
     */
    public function find(callable $predicate);

    /**
     * Builds a new collection by applying a function to all elements of the pipeable.
     * and using the elements of the resulting collections.
     * @param callable $mappingFunction A function which maps each element of this collection to an array or a Traversable.
     * @return Pipeable
     */
    public function flatMap(callable $mappingFunction);

    /**
     * Tests whether a predicate holds for all elements of the pipeable.
     * @param callable $predicate
     * @return bool
     * @see any
     */
    public function all(callable $predicate);

    /**
     * Tests whether a predicate holds for any element of the pipeable.
     * @param callable $predicate
     * @return bool
     * @see all
     */
    public function any(callable $predicate);

    /**
     * Partitions the pipeable into a map of traversable collections according to some discriminator function.
     * <p>Note: this method is not re-implemented by views.
     * This means when applied to a view it will always force the view and return a new traversable collection.</p>
     * @param callable $keyFunction
     * @return Collection
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
     * Applies the binary function to all elements of the pipeable with initial as the first value, if given.
     * Throws a {@link NoSuchElementException} If the traversable is empty and initial is not given.
     * @param callable $binaryFunction
     * @param mixed $initial
     * @return mixed
     * @throws NoSuchElementException
     */
    public function reduce(callable $binaryFunction, $initial = null);

    /**
     * Applies the binary function to all elements of the pipeable with initial as the first value, if given.
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
     * @return Pipeable
     * @see take
     * @see drop
     */
    public function slice($from, $until);

    /**
     * Apply function f to all elements in this pipeable.
     * If the callback accepts one argument, is is called with $f(value).
     * If it accepts two arguments, it is called with $f(key, value).
     * @param callable $f
     */
    public function each(callable $f);

    /**
     * Returns a new pipeable with the keys of this pipeable as values and their index as keys.
     * @return Pipeable
     */
    public function keys();

    /**
     * Returns a new pipeable with the values of this pipeable as values and their index as keys.
     * @return Pipeable
     */
    public function values();

    /**
     * Transforms this pipeable to a new pipeable with the current (key, value) pairs as values and their index as keys.
     * @return Pipeable
     */
    public function entries();

    /**
     * Tests if this collection is empty.
     * @return bool
     */
    public function isEmpty();

    /**
     * Creates a new pipeable containing all items in this pipeable sorted, either according to their natural order,
     * or by using the results of the supplied compareFunction.
     * @param callable $compareFunction A function accepting two [key, value] pairs returning an integer less than,
     * equal to, or greater than zero if the first argument is considered to be respectively less than, equal to,
     * or greater than the second.
     * <p>Note: this method is not re-implemented by views.
     * This means when applied to a view it will always force the view and return a new traversable collection.</p>
     * @return Pipeable
     * @see usort
     * @see sortBy
     */
    public function sort(callable $compareFunction = null);

    /**
     * Creates a new pipeable containing all items in this pipeable sorted by the result of the mappingFunction.
     * <p>This implementation first applies the mapping function to each entry, than sorts a temporary pipeable by the computed keys.
     * From this pipeable a new pipeable it created by extracting the entries at the resulting index.</p>
     * <p>Note: this method is not re-implemented by views.
     * This means when applied to a view it will always force the view and return a new traversable collection.</p>
     * @param callable $mappingFunction
     * @return Pipeable
     * @see sort
     */
    public function sortBy(callable $mappingFunction);

    /**
     * Transforms this pipeable into an array.
     * @param bool $preserveKeys indicates if the keys should be preserved or if values should be re-indexed (Has no effect for vectors).
     * @return array
     */
    public function toArray($preserveKeys = true);

    /**
     * Returns this pipeable as a traversable collection.
     * <p>This function is primarily a workaround to the inability of traits to implement interfaces.
     * Because all classes implementing Pipeable should also implement {@link Traversable},
     * most implementation simply return <code>$this</code>.</p>
     * @return Traversable
     */
    public function asTraversable();

    /**
     * @return Iterator
     */
    public function getIterator();

    /**
     * Returns a new pipeable, whose methods compute values lazily.
     * @return Pipeable
     */
    public function view();

    /**
     * Returns a new pipeable, whose methods compute values directly.
     * @return Pipeable
     */
    public function force();

    /**
     * @return Builder
     */
    public function builder();

    /**
     * @return bool
     */
    public function isVectorLike();
}