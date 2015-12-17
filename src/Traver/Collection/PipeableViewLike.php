<?php


namespace Traver\Collection;


use Traver\Exception\UnsupportedOperationException;

/**
 * Class PipeableViewLike is used to implement a lazy version of {@link Pipeable}.
 * @package Traver\Collection
 */
trait PipeableViewLike
{
    use PipeableLike;

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
        $forced = $this->force();
        $grouped = $forced->groupBy($keyFunction);

        return $grouped->map(function ($group) {
            return $this->delegate()->builder()->addAll($group)->build();
        });
    }

    public function force()
    {
        return $this->delegate()->builder()->addAll($this->asTraversable())->build();
    }

    /**
     * @return Pipeable
     */
    abstract protected function delegate();

    public function sort(callable $compareFunction = null)
    {
        $forced = $this->force();
        $sorted = $forced->sort($compareFunction);
        return $sorted;
    }

    public function sortBy(callable $mappingFunction)
    {
        $forced = $this->force();
        $sorted = $forced->sortBy($mappingFunction);
        return $sorted;
    }

    public function flatten($level = -1)
    {
        return new View\Flattened($this, $level);
    }

    public function isVectorLike()
    {
        return $this->delegate()->isVectorLike();
    }

    public function view()
    {
        return $this;
    }

    final public function builder()
    {
        throw new UnsupportedOperationException("Views do not have a builder.");
    }
}