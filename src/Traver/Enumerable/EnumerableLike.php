<?php


namespace Traver\Enumerable;


use PhpOption\None;
use PhpOption\Option;
use Traver\Exception\NoSuchElementException;
use Traver\Exception\UnsupportedOperationException;
use Traversable;

trait EnumerableLike
{
    /**
     * @param callable $mappingFunction
     * @return Enumerable
     */
    public function map(callable $mappingFunction)
    {
        $builder = $this->builder();
        foreach ($this->asTraversable() as $key => $element) {
            $builder->add($mappingFunction($element), $key);
        }
        return $builder->build();
    }

    /**
     * @see Enumerable::head()
     * @return mixed
     */
    public function head()
    {
        $result = function () {
            throw new NoSuchElementException();
        };
        foreach ($this->asTraversable() as $element) {
            $result = function () use ($element) {
                return $element;
            };
            break;
        }
        return $result();
    }

    /**
     * @see Enumerable::head()
     */
    public function tail()
    {
        if ($this->isEmpty()) {
            throw new UnsupportedOperationException();
        }
        return $this->drop(1);
    }

    /**
     * @see Enumerable::isEmpty()
     */
    public function isEmpty()
    {
        $result = true;
        foreach ($this->asTraversable() as $element) {
            $result = false;
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function toArray($preserveKeys = true)
    {
        return iterator_to_array($this->asTraversable(), $preserveKeys);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return iterator_count($this->asTraversable());
    }

    /**
     * @inheritDoc
     */
    public function countWhich(callable $predicate)
    {
        return iterator_count($this->filter($predicate));
    }

    /**
     * @inheritDoc
     */
    public function drop($n)
    {
        return $this->slice($n, PHP_INT_MAX);
    }

    /**
     * @param int $from
     * @param int $until
     * @return Enumerable
     */
    public function slice($from, $until)
    {
        $builder = $this->builder();
        if ($until > $from) {
            $i = 0;
            foreach ($this->asTraversable() as $key => $element) {
                if ($i >= $from) {
                    $builder->add($element, $key);
                }
                $i++;
                if ($i >= $until) {
                    break;
                }
            }
        }
        return $builder->build();
    }

    /**
     * @inheritDoc
     */
    public function dropWhile(callable $predicate)
    {
        $builder = $this->builder();
        $go = false;
        foreach ($this->asTraversable() as $key => $element) {
            if (!$go && !$predicate($element, $key)) {
                $go = true;
            }
            if ($go) {
                $builder->add($element, $key);
            }
        }
        return $builder->build();
    }

    /**
     * @inheritDoc
     */
    public function exists(callable $predicate)
    {
        foreach ($this as $key => $value) {
            if ($predicate($value, $key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function filter(callable $predicate)
    {
        $builder = $this->builder();
        foreach ($this->asTraversable() as $key => $element) {
            if ($predicate($element, $key)) {
                $builder->add($element, $key);
            }
        }
        return $builder->build();
    }

    /**
     * @inheritDoc
     */
    public function filterNot(callable $predicate)
    {
        return $this->filter(function ($element, $key) use ($predicate) {
            return !$predicate($element, $key);
        });
    }

    /**
     * @param callable $predicate
     * @return mixed
     */
    public function find(callable $predicate)
    {
        $result = None::create();
        foreach ($this->asTraversable() as $key => $element) {
            if ($predicate($element, $key)) {
                $result = Option::fromValue($element);
            }
        }
        return $result;
    }

    /**
     * @see Enumerable::flatMap()
     * @param callable $mappingFunction A function which maps each element of this collection to an array or a Traversable.
     * @return Enumerable
     */
    public function flatMap(callable $mappingFunction)
    {
        $builder = $this->builder();
        foreach ($this->asTraversable() as $key => $element) {
            $array = $mappingFunction($element, $key);
            if (is_array($array) || $array instanceof Traversable) {
                $builder->addAll($array, false);
            } else {
                $builder->add($array);
            }
        }
        return $builder->build();
    }

    /**
     * Applies a binary operator to a start value and all elements of this traversable or iterator, going left to right.
     * @param mixed $initialValue
     * @param callable $binaryFunction
     * @return mixed
     * @see Enumerable::foldLeft
     */
    public function foldLeft($initialValue, callable $binaryFunction)
    {
        $result = $initialValue;
        foreach ($this->asTraversable() as $key => $value) {
            $result = $binaryFunction($result, $value, $key);
        }
        return $result;
    }

    /**
     * Applies a binary operator to a start value and all elements of this traversable or iterator, going right to left.
     * @param mixed $initialValue
     * @param callable $binaryFunction
     * @return mixed
     * @see Enumerable::foldRight
     */
    public function foldRight($initialValue, callable $binaryFunction)
    {
        $this->reversed()->foldLeft($initialValue, $binaryFunction);
    }

    protected function reversed()
    {
        return $this->builder()->addAll(array_reverse($this->toArray()))->build();
    }

    /**
     * Tests whether a predicate holds for all elements of this traversable collection.
     * @param callable $predicate
     * @return bool
     * @see Enumerable::forall
     */
    public function forall(callable $predicate)
    {
        foreach ($this->asTraversable() as $key => $value) {
            if (!$predicate($value, $key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param callable $keyFunction
     * @return Enumerable
     * @see Enumerable::groupBy
     */
    public function groupBy(callable $keyFunction)
    {
        $result = [];
        foreach ($this->asTraversable() as $key => $value) {
            $groupKey = $keyFunction($value, $key);
            if (!isset($result[$groupKey])) {
                $result[$groupKey] = [];
            }
            $result[$groupKey][$key] = $value;
        }

        $map = new ArrayObjectEnumerable();
        foreach ($result as $key => $group) {
            $groupEnumerable = $this->builder()->addAll($group)->build();
            $map[$key] = $groupEnumerable;
        }
        return $map;
    }

    /**
     * @inheritDoc
     */
    public function keys()
    {
        return $this->map(function ($value, $key) {
            return $key;
        });
    }

    /**
     * @param callable $f
     */
    public function each(callable $f)
    {
        foreach ($this->asTraversable() as $key => $element) {
            $f($element, $key);
        }
    }

    /**
     * @return Traversable
     */
    abstract public function asTraversable();

    /**
     * @return Builder
     */
    abstract protected function builder();
}