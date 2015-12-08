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
        $mappingFunction = self::wrapCallback($mappingFunction);
        /** @noinspection PhpUnusedParameterInspection */
        return new Transformed($this, function ($key, $value, $index) use ($mappingFunction) {
            return [$index, $mappingFunction($value, $key)];
        });
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
        $predicate = self::wrapCallback($predicate);
        return new DroppedWhile($this, $predicate);
    }

    public function take($n)
    {
        return new Sliced($this, 0, $n);
    }

    public function takeWhile(callable $predicate)
    {
        $predicate = self::wrapCallback($predicate);
        return new TakenWhile($this, $predicate);
    }

    public function select(callable $predicate)
    {
        $predicate = self::wrapCallback($predicate);
        return new Filtered($this, $predicate);
    }

    public function reject(callable $predicate)
    {
        $predicate = self::wrapCallback($predicate);
        return new Filtered($this, function ($key, $value) use ($predicate) {
            return !$predicate($key, $value);
        });
    }

    public function flatMap(callable $mappingFunction)
    {
        $mappingFunction = self::wrapCallback($mappingFunction);
        return new FlatMapped($this, $mappingFunction);
    }

    public function keys()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return new Transformed($this, function ($key, $value, $index) {
            return [$index, $key];
        });
    }

    public function values()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return new Transformed($this, function ($key, $value, $index) {
            return [$index, $value];
        });
    }

    public function entries()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return new Transformed($this, function ($key, $value, $index) {
            return [$index, [$key, $value]];
        });
    }

    public function slice($from, $until)
    {
        return new Sliced($this, $from, $until);
    }

    public function groupBy(callable $keyFunction)
    {
        $keyFunction = self::wrapCallback($keyFunction);
        $mutableCopy = new ArrayObjectEnumerable($this->toArray());
        $grouped = $mutableCopy->groupBy($keyFunction);
        return \Traver\view($grouped->asTraversable());
    }

    /**
     * @inheritDoc
     */
    public function sort(callable $compareFunction = null)
    {
        $mutableCopy = new ArrayObjectEnumerable($this->toArray());
        $sorted = $mutableCopy->sort($compareFunction);
        return \Traver\view($sorted->asTraversable());
    }

    /**
     * @inheritDoc
     */
    public function sortBy(callable $mappingFunction)
    {
        $mutableCopy = new ArrayObjectEnumerable($this->toArray());
        $sorted = $mutableCopy->sortBy($mappingFunction);
        return \Traver\view($sorted->asTraversable());
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