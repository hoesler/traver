<?php


namespace Traver\Enumerable;


use CallbackFilterIterator;
use Iterator;
use IteratorAggregate;
use LimitIterator;
use Traver\Exception\UnsupportedOperationException;
use Traver\Iterator\CallbackLimitIterator;
use Traver\Iterator\CallbackOffsetIterator;
use Traver\Iterator\ConcatIterator;
use Traver\Iterator\MappingIterator;
use Traver\Iterator\TransformingIterator;

trait EnumerableViewLike
{
    use EnumerableLike;

    /**
     * @var \IteratorAggregate
     */
    private $delegate;

    public function map(callable $mappingFunction)
    {
        return new Mapped($this, $mappingFunction);
    }

    public function transform(callable $mappingFunction)
    {
        return new Transformed($this, $mappingFunction);
    }

    public function tail()
    {
        if ($this->isEmpty()) {
            throw new UnsupportedOperationException();
        }
        return new Sliced($this, 1);
    }

    public function drop($n)
    {
        return new Sliced($this, $n);
    }

    public function dropWhile(callable $predicate)
    {
        return new DroppedWhile($this, $predicate);
    }

    public function take($n)
    {
        return new Sliced($this, 0, $n);
    }

    public function takeWhile(callable $predicate)
    {
        return new TakenWhile($this, $predicate);
    }

    public function select(callable $predicate)
    {
        return new Filtered($this, $predicate);
    }

    public function reject(callable $predicate)
    {
        return new Filtered($this, function ($value, $key) use ($predicate) {
            return !$predicate($value, $key);
        });
    }

    public function flatMap(callable $mappingFunction)
    {
        return new FlatMapped($this, $mappingFunction);
    }

    public function keys()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return new Transformed($this, function ($key, $value, $index) {
            return [$index, $key];
        }, false);
    }

    public function slice($from, $until)
    {
        return new Sliced($this, $from, $until);
    }

    public function groupBy(callable $keyFunction)
    {
        $arrayObjectEnumerable = new ArrayObjectEnumerable($this->toArray());
        return $arrayObjectEnumerable->groupBy($keyFunction);
    }

    /**
     * @return Iterator
     */
    abstract function getIterator();

    public function asTraversable()
    {
        return $this->getIterator();
    }

    /**
     * @codeCoverageIgnore
     */
    final protected function builder()
    {
        throw new UnsupportedOperationException("EnumerableView does not support builder");
    }
}

/**
 * Class Filtered
 * @package Traver\Enumerable
 * @codeCoverageIgnore
 * @internal
 */
class Filtered implements \IteratorAggregate, Enumerable
{
    use EnumerableViewLike;

    /**
     * @var callable
     */
    private $predicate;

    /**
     * Dropped constructor.
     * @param EnumerableViewLike $delegate
     * @param callable $predicate
     */
    public function __construct($delegate, $predicate)
    {
        $this->delegate = $delegate;
        $this->predicate = $predicate;
    }

    public function getIterator()
    {
        return new CallbackFilterIterator($this->delegate->getIterator(), $this->predicate);
    }
}

/**
 * Class DroppedWhile
 * @package Traver\Enumerable
 * @codeCoverageIgnore
 * @internal
 */
class DroppedWhile implements \IteratorAggregate, Enumerable
{
    use EnumerableViewLike;

    /**
     * @var callable
     */
    private $predicate;

    /**
     * Dropped constructor.
     * @param EnumerableViewLike $delegate
     * @param callable $predicate
     */
    public function __construct($delegate, $predicate)
    {
        $this->delegate = $delegate;
        $this->predicate = $predicate;
    }

    public function getIterator()
    {
        return new CallbackOffsetIterator($this->delegate->getIterator(), $this->predicate);
    }
}

/**
 * Class TakenWhile
 * @package Traver\Enumerable
 * @codeCoverageIgnore
 * @internal
 */
class TakenWhile implements \IteratorAggregate, Enumerable
{
    use EnumerableViewLike;

    /**
     * @var callable
     */
    private $predicate;

    /**
     * Dropped constructor.
     * @param EnumerableViewLike $delegate
     * @param callable $predicate
     */
    public function __construct($delegate, $predicate)
    {
        $this->delegate = $delegate;
        $this->predicate = $predicate;
    }

    public function getIterator()
    {
        return new CallbackLimitIterator($this->delegate->getIterator(), $this->predicate);
    }
}

/**
 * Class Sliced
 * @package Traver\Enumerable
 * @codeCoverageIgnore
 * @internal
 */
class Sliced implements \IteratorAggregate, Enumerable
{
    use EnumerableViewLike;

    /**
     * @var
     */
    private $from;

    /**
     * @var
     */
    private $until;

    /**
     * MapView constructor.
     * @param EnumerableViewLike $delegate
     * @param $from
     * @param $until
     */
    public function __construct($delegate, $from = 0, $until = PHP_INT_MAX)
    {
        $this->delegate = $delegate;
        $this->from = $from;
        $this->until = $until;
    }

    public function getIterator()
    {
        $offset = $this->from;
        $count = max($this->until - $this->from, 0);
        if ($count == 0) {
            return new \EmptyIterator();
        } else {
            return new LimitIterator($this->delegate->getIterator(), $offset, $count);
        }
    }
}

/**
 * Class Mapped
 * @package Traver\Enumerable
 * @codeCoverageIgnore
 * @internal
 */
class Mapped implements \IteratorAggregate, Enumerable
{
    use EnumerableViewLike;

    /**
     * @var callable
     */
    private $mappingFunction;

    /**
     * MapView constructor.
     * @param EnumerableViewLike $delegate
     * @param callable $mappingFunction
     */
    public function __construct($delegate, $mappingFunction)
    {
        $this->mappingFunction = $mappingFunction;
        $this->delegate = $delegate;
    }

    public function getIterator()
    {
        return new MappingIterator($this->delegate->getIterator(), $this->mappingFunction);
    }
}

/**
 * Class Transformed
 * @package Traver\Enumerable
 * @codeCoverageIgnore
 * @internal
 */
class Transformed implements \IteratorAggregate, Enumerable
{
    use EnumerableViewLike;

    /**
     * @var callable
     */
    private $mappingFunction;

    /**
     * MapView constructor.
     * @param EnumerableViewLike $delegate
     * @param callable $mappingFunction
     */
    public function __construct($delegate, $mappingFunction)
    {
        $this->mappingFunction = $mappingFunction;
        $this->delegate = $delegate;
    }

    public function getIterator()
    {
        return new TransformingIterator($this->delegate->getIterator(), $this->mappingFunction);
    }
}

/**
 * Class FlatMapped
 * @package Traver\Enumerable
 * @codeCoverageIgnore
 * @internal
 */
class FlatMapped implements \IteratorAggregate, Enumerable
{
    use EnumerableViewLike;

    /**
     * @var callable
     */
    private $mappingFunction;

    /**
     * MapView constructor.
     * @param EnumerableViewLike $delegate
     * @param callable $mappingFunction
     */
    public function __construct($delegate, $mappingFunction)
    {
        $this->mappingFunction = $mappingFunction;
        $this->delegate = $delegate;
    }

    public function getIterator()
    {
        return new ConcatIterator(
            new MappingIterator(
                new MappingIterator($this->delegate->getIterator(), $this->mappingFunction),
                function ($element) {
                    if ($element instanceof \Traversable) {
                        return new \IteratorIterator($element);
                    } elseif (is_array($element)) {
                        return new \ArrayIterator($element);
                    } else {
                        return new \ArrayIterator([$element]); // TODO Implement a singleton iterator
                    }
                })
        );
    }
}