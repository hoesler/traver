<?php


namespace Traver\Enumerable;


use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use Traver\Exception\NoSuchElementException;
use Traver\Exception\UnsupportedOperationException;
use Traversable;

/**
 * Class EnumerableLike
 * @package Traver\Enumerable
 * @implements Enumerable
 */
trait EnumerableLike
{
    /**
     * Implements {@link Enumerable::map}.
     * @param callable $mappingFunction
     * @param bool $preserveKeys
     * @return Enumerable
     */
    public function map(callable $mappingFunction, $preserveKeys = true)
    {
        $builder = $this->builder();
        $index = 0;
        foreach ($this->asTraversable() as $key => $element) {
            $builder->add($mappingFunction($element, $key), ($preserveKeys) ? $key : $index);
            $index++;
        }
        return $builder->build();
    }

    /**
     * Implements {@link Enumerable::head}.
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
     * Implements {@link Enumerable::tail}.
     */
    public function tail()
    {
        if ($this->isEmpty()) {
            throw new UnsupportedOperationException();
        }
        return $this->drop(1);
    }

    /**
     * Implements {@link Enumerable::isEmpty}.
     */
    public function isEmpty()
    {
        $result = true;
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($this->asTraversable() as $element) {
            $result = false;
        }
        return $result;
    }

    /**
     * Implements {@link Enumerable::toArray}.
     * @param bool $preserveKeys
     * @return array
     */
    public function toArray($preserveKeys = true)
    {
        return iterator_to_array($this->asTraversable(), $preserveKeys);
    }

    /**
     * Implements {@link Enumerable::count}.
     */
    public function count()
    {
        return iterator_count($this->asTraversable());
    }

    /**
     * Implements {@link Enumerable::countWhich}.
     * @param callable $predicate
     * @return int
     */
    public function countWhich(callable $predicate)
    {
        return iterator_count($this->select($predicate));
    }

    /**
     * Implements {@link Enumerable::drop}.
     * @param $n
     * @return Enumerable
     */
    public function drop($n)
    {
        return $this->slice($n, PHP_INT_MAX);
    }

    /**
     * Implements {@link Enumerable::slice}.
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
     * Implements {@link Enumerable::dropWhile}.
     * @param callable $predicate
     * @return Enumerable
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
     * Implements {@link Enumerable::take}.
     * @param int $n
     * @return Enumerable
     */
    public function take($n)
    {
        return $this->slice(0, $n);
    }

    /**
     * Implements {@link Enumerable::takeWhile}.
     * @param callable $predicate
     * @return Enumerable
     */
    public function takeWhile(callable $predicate)
    {
        $builder = $this->builder();
        foreach ($this->asTraversable() as $key => $element) {
            if (!$predicate($element, $key)) {
                break;
            }
            $builder->add($element, $key);
        }
        return $builder->build();
    }

    /**
     * Implements {@link Enumerable::exists}.
     * @param callable $predicate
     * @return bool
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
     * Implements {@link Enumerable::select}.
     * @param callable $predicate
     * @return Enumerable
     */
    public function select(callable $predicate)
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
     * Implements {@link Enumerable::reject}.
     * @param callable $predicate
     * @return Enumerable
     */
    public function reject(callable $predicate)
    {
        return $this->select(function ($element, $key) use ($predicate) {
            return !$predicate($element, $key);
        });
    }

    /**
     * Implements {@link Enumerable::find}.
     * @param callable $predicate
     * @return Option
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
     * Implements {@link Enumerable::flatMap}.
     * @param callable $mappingFunction
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
     * Implements {@link Enumerable::all}.
     * @param callable $predicate
     * @return bool
     */
    public function all(callable $predicate)
    {
        foreach ($this->asTraversable() as $key => $value) {
            if (!$predicate($value, $key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Implements {@link Enumerable::any}.
     * @param callable $predicate
     * @return bool
     */
    public function any(callable $predicate)
    {
        foreach ($this->asTraversable() as $key => $value) {
            if ($predicate($value, $key)) {
                return true;
            }
        }
        return false;
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
     * Concatenates the strval of all elements separated by the given separator.
     * @param $separator
     * @return mixed
     * @see Enumerable::join
     */
    public function join($separator = '')
    {
        return $this->reduceOption(function ($joined, $item) use ($separator) {
            return $joined . $separator . $item;
        })->getOrElse('');
    }

    /**
     * Implements {@link Enumerable::reduce}.
     * @param callable $binaryFunction
     * @param mixed $initial
     * @return mixed
     * @throws NoSuchElementException
     */
    public function reduce(callable $binaryFunction, $initial = null)
    {
        $isInitialArgumentAbsent = (func_num_args() == 1);

        if ($this->isEmpty() && $isInitialArgumentAbsent) {
            throw new UnsupportedOperationException("empty.reduce");
        }

        $result = $initial;
        $first = $isInitialArgumentAbsent;
        foreach ($this->asTraversable() as $key => $item) {
            if ($first) {
                $result = $item;
                $first = false;
            } else {
                $result = $binaryFunction($result, $item, $key);
            }
        }
        return $result;
    }

    /**
     * Implements {@link Enumerable::reduceOption}.
     * @param callable $binaryFunction
     * @param mixed $initial
     * @return Option
     */
    public function reduceOption(callable $binaryFunction, $initial = null)
    {
        if (func_num_args() == 1) {
            if ($this->isEmpty()) {
                return None::create();
            } else {
                return Some::create($this->reduce($binaryFunction));
            }
        } else {
            return Some::create($this->reduce($binaryFunction, $initial));
        }
    }

    /**
     * Implements {@link Enumerable::keys}.
     * @return Enumerable
     */
    public function keys()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return $this->map(function ($value, $key) {
            return $key;
        }, false);
    }

    /**
     * Implements {@link Enumerable::each}.
     * @param callable $f
     */
    public function each(callable $f)
    {
        foreach ($this->asTraversable() as $key => $element) {
            $f($element, $key);
        }
    }

    /**
     * Implements {@link Enumerable::asTraversable}.
     * @return Traversable
     */
    abstract public function asTraversable();

    /**
     * Creates a new Builder for the current class implementing Enumerable.
     * @return Builder
     */
    abstract protected function builder();
}