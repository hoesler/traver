<?php


namespace Traver\Enumerable;


use CallbackFilterIterator;
use Iterator;
use IteratorAggregate;
use LimitIterator;
use Traver\Exception\UnsupportedOperationException;
use Traver\Iterator\CallbackOffsetIterator;
use Traver\Iterator\ConcatIterator;
use Traver\Iterator\MappingIterator;

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

    /**
     * @inheritDoc
     */
    public function tail()
    {
        if ($this->isEmpty()) {
            throw new UnsupportedOperationException();
        }
        return new Sliced($this, 1);
    }

    /**
     * @inheritDoc
     */
    public function drop($n)
    {
        return new Dropped($this, $n);
    }

    /**
     * @inheritDoc
     */
    public function dropWhile(callable $predicate)
    {
        return new DroppedWhile($this, $predicate);
    }

    /**
     * @inheritDoc
     */
    public function filter(callable $predicate)
    {
        return new Filtered($this, $predicate);
    }

    /**
     * @inheritDoc
     */
    public function filterNot(callable $predicate)
    {
        return new Filtered($this, function ($value, $key) use ($predicate) {
            return !$predicate($value, $key);
        });
    }

    /**
     * @inheritDoc
     */
    public function flatMap(callable $mappingFunction)
    {
        return new FlatMapped($this, $mappingFunction);
    }


    /**
     * @inheritDoc
     */
    public function keys()
    {
        return new Mapped($this, function ($value, $key) {
            return $key;
        });
    }

    /**
     * @param int $from
     * @param int $until
     * @return Enumerable
     */
    public function slice($from, $until)
    {
        return new Sliced($this, $from, $until);
    }

    /**
     * @return Iterator
     */
    abstract function getIterator();

    /**
     * @inheritDoc
     */
    public function asTraversable()
    {
        return $this->getIterator();
    }

    protected function builder()
    {
        throw new UnsupportedOperationException("EnumerableView does not support builder");
    }
}

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

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new CallbackFilterIterator($this->delegate->getIterator(), $this->predicate);
    }
}

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

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new CallbackOffsetIterator($this->delegate->getIterator(), $this->predicate);
    }
}

class Dropped implements \IteratorAggregate, Enumerable
{
    use EnumerableViewLike;

    /**
     * @var int
     */
    private $n;

    /**
     * Dropped constructor.
     * @param EnumerableViewLike $delegate
     * @param int $n
     */
    public function __construct($delegate, $n)
    {
        $this->delegate = $delegate;
        $this->n = $n;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new LimitIterator($this->delegate->getIterator(), $this->n);
    }
}

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

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new MappingIterator($this->delegate->getIterator(), $this->mappingFunction);
    }
}

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

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ConcatIterator(
            new MappingIterator(
                new MappingIterator($this->delegate->getIterator(), $this->mappingFunction),
                function ($element) {
                    if ($element instanceof \Iterator) {
                        return $element;
                    } elseif (is_array($element)) {
                        return new \ArrayIterator($element);
                    } else {
                        throw new \RuntimeException("Element must be mapped to an array or an iterator");
                    }
                })
        );
    }
}