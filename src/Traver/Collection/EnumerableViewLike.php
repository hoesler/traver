<?php


namespace Traver\Collection;


use Iterator;
use Traver\Exception\UnsupportedOperationException;

trait EnumerableViewLike
{
    use EnumerableLike;

    public function map(callable $mappingFunction)
    {
        $mappingFunction = self::wrapCallback($mappingFunction);
        /** @noinspection PhpUnusedParameterInspection */
        return new View\Transformed($this, function ($key, $value, $index) use ($mappingFunction) {
            return [$key, $mappingFunction($value, $key)];
        });
    }

    public function tail()
    {
        if ($this->isEmpty()) {
            throw new UnsupportedOperationException();
        }
        return new View\Sliced($this, 1);
    }

    public function drop($n)
    {
        return new View\Sliced($this, $n);
    }

    public function dropWhile(callable $predicate)
    {
        $predicate = self::wrapCallback($predicate);
        return new View\DroppedWhile($this, $predicate);
    }

    public function take($n)
    {
        return new View\Sliced($this, 0, $n);
    }

    public function takeWhile(callable $predicate)
    {
        $predicate = self::wrapCallback($predicate);
        return new View\TakenWhile($this, $predicate);
    }

    public function select(callable $predicate)
    {
        $predicate = self::wrapCallback($predicate);
        return new View\Filtered($this, $predicate);
    }

    public function reject(callable $predicate)
    {
        $predicate = self::wrapCallback($predicate);
        return new View\Filtered($this, function ($key, $value) use ($predicate) {
            return !$predicate($key, $value);
        });
    }

    public function flatMap(callable $mappingFunction)
    {
        $mappingFunction = self::wrapCallback($mappingFunction);
        return new View\FlatMapped($this, $mappingFunction);
    }

    public function keys()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return new View\Transformed($this, function ($key, $value, $index) {
            return [$index, $key];
        });
    }

    public function values()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return new View\Transformed($this, function ($key, $value, $index) {
            return [$index, $value];
        });
    }

    public function entries()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return new View\Transformed($this, function ($key, $value, $index) {
            return [$index, [$key, $value]];
        });
    }

    public function slice($from, $until)
    {
        return new View\Sliced($this, $from, $until);
    }

    public function groupBy(callable $keyFunction)
    {
        $keyFunction = self::wrapCallback($keyFunction);
        $mutableCopy = $this->mutableCopy();
        $grouped = $mutableCopy->groupBy($keyFunction);

        return $grouped->map(function ($group) {
            return $this->newCollection($group);
        });
    }

    public function sort(callable $compareFunction = null)
    {
        $mutableCopy = $this->mutableCopy();
        $sorted = $mutableCopy->sort($compareFunction);

        return $this->newCollection($sorted->asTraversable());
    }

    public function sortBy(callable $mappingFunction)
    {
        $mutableCopy = $this->mutableCopy();
        $sorted = $mutableCopy->sortBy($mappingFunction);

        return $this->newCollection($sorted->asTraversable());
    }

    final public function asTraversable()
    {
        return $this->getIterator();
    }

    /**
     * @return Iterator
     */
    abstract function getIterator();
}