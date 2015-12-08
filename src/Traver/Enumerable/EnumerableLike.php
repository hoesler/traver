<?php


namespace Traver\Enumerable;


use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use Traver\Callback\Callbacks;
use Traver\Callback\Comparators;
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
     * @return EnumerableLike
     */
    public function map(callable $mappingFunction)
    {
        $mappingFunction = self::wrapCallback($mappingFunction);
        $builder = $this->builder();
        foreach ($this->asTraversable() as $key => $value) {
            $builder->add($mappingFunction($value, $key), $key);
        }
        return $builder->build();
    }

    /**
     * @param callable $transformationFunction
     * @return EnumerableLike
     * @codeCoverageIgnore
     * @internal
     */
    private function transform(callable $transformationFunction)
    {
        $builder = $this->builder();
        $index = 0;
        foreach ($this->asTraversable() as $key => $value) {
            list($newKey, $newValue) = $transformationFunction($key, $value, $index);
            $builder->add($newValue, $newKey);
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
     * @return EnumerableLike
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
     * @return bool
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
     * @return int
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
        $predicate = self::wrapCallback($predicate);
        return iterator_count($this->select($predicate)->asTraversable());
    }

    /**
     * Implements {@link Enumerable::drop}.
     * @param $n
     * @return EnumerableLike
     */
    public function drop($n)
    {
        return $this->slice($n, PHP_INT_MAX);
    }

    /**
     * Implements {@link Enumerable::slice}.
     * @param int $from
     * @param int $until
     * @return EnumerableLike
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
     * @return EnumerableLike
     */
    public function dropWhile(callable $predicate)
    {
        $predicate = self::wrapCallback($predicate);
        $builder = $this->builder();
        $go = false;
        foreach ($this->asTraversable() as $key => $value) {
            if (!$go && !$predicate($value, $key)) {
                $go = true;
            }
            if ($go) {
                $builder->add($value, $key);
            }
        }
        return $builder->build();
    }

    /**
     * Implements {@link Enumerable::take}.
     * @param int $n
     * @return EnumerableLike
     */
    public function take($n)
    {
        return $this->slice(0, $n);
    }

    /**
     * Implements {@link Enumerable::takeWhile}.
     * @param callable $predicate
     * @return EnumerableLike
     */
    public function takeWhile(callable $predicate)
    {
        $predicate = self::wrapCallback($predicate);
        $builder = $this->builder();
        foreach ($this->asTraversable() as $key => $value) {
            if (!$predicate($value, $key)) {
                break;
            }
            $builder->add($value, $key);
        }
        return $builder->build();
    }

    /**
     * Implements {@link Enumerable::select}.
     * @param callable $predicate
     * @return EnumerableLike
     */
    public function select(callable $predicate)
    {
        $predicate = self::wrapCallback($predicate);
        $builder = $this->builder();
        foreach ($this->asTraversable() as $key => $value) {
            $accepted = $predicate($value, $key);
            if ($accepted) {
                $builder->add($value, $key);
            }
        }
        return $builder->build();
    }

    /**
     * Implements {@link Enumerable::reject}.
     * @param callable $predicate
     * @return EnumerableLike
     */
    public function reject(callable $predicate)
    {
        $predicate = self::wrapCallback($predicate);
        $builder = $this->builder();
        foreach ($this->asTraversable() as $key => $value) {
            $rejected = $predicate($value, $key);
            if (!$rejected) {
                $builder->add($value, $key);
            }
        }
        return $builder->build();
    }

    /**
     * Implements {@link Enumerable::find}.
     * @param callable $predicate
     * @return Option
     */
    public function find(callable $predicate)
    {
        $predicate = self::wrapCallback($predicate);
        $result = None::create();
        foreach ($this->asTraversable() as $key => $value) {
            if ($predicate($value, $key)) {
                $result = Option::fromValue($value);
            }
        }
        return $result;
    }

    /**
     * Implements {@link Enumerable::flatMap}.
     * @param callable $mappingFunction
     * @return EnumerableLike
     */
    public function flatMap(callable $mappingFunction)
    {
        $mappingFunction = self::wrapCallback($mappingFunction);
        $builder = $this->builder();
        foreach ($this->asTraversable() as $key => $value) {
            $array = $mappingFunction($value, $key);
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
        $predicate = self::wrapCallback($predicate);
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
        $predicate = self::wrapCallback($predicate);
        foreach ($this->asTraversable() as $key => $value) {
            if ($predicate($value, $key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param callable $keyFunction
     * @return EnumerableLike
     * @see Enumerable::groupBy
     */
    public function groupBy(callable $keyFunction)
    {
        $keyFunction = self::wrapCallback($keyFunction);
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
     * @return EnumerableLike
     */
    public function keys()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return $this->transform(function ($key, $value, $index) {
            return [$index, $key];
        });
    }

    /**
     * Implements {@link Enumerable::values}.
     * @return EnumerableLike
     */
    public function values()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return $this->transform(function ($key, $value, $index) {
            return [$index, $value];
        });
    }

    /**
     * Implements {@link Enumerable::entries}.
     * @return EnumerableLike
     */
    public function entries()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return $this->transform(function ($key, $value, $index) {
            return [$index, [$key, $value]];
        });
    }

    /**
     * Implements {@link Enumerable::each}.
     * @param callable $f
     */
    public function each(callable $f)
    {
        $f = self::wrapCallback($f);
        foreach ($this->asTraversable() as $key => $value) {
            $f($value, $key);
        }
    }

    /**
     * Implements {@link Enumerable::sort}.
     * @param callable|null $compareFunction
     * @return EnumerableLike
     */
    public function sort(callable $compareFunction = null)
    {
        if ($compareFunction === null) {
            $compareFunction = Comparators::naturalComparator();
        }
        $sortedEntries = $this->entries()->toArray();
        $success = uasort($sortedEntries, $compareFunction);
        if ($success === false) {
            throw new \RuntimeException("sort failed");
        }
        $builder = $this->builder();
        foreach ($sortedEntries as $entry) {
            list($key, $value) = $entry;
            $builder->add($value, $key);
        }
        return $builder->build();
    }

    /**
     * Implements {@link Enumerable::sortBy}.
     * @param callable $mappingFunction
     * @return EnumerableLike
     */
    public function sortBy(callable $mappingFunction)
    {
        $mappingFunction = self::wrapCallback($mappingFunction);
        /** @noinspection PhpUnusedParameterInspection */
        return $this
            ->transform(function ($key, $value, $index) use ($mappingFunction) {
                return [$index, [$mappingFunction($value, $key), [$key, $value]]];
            })
            ->sort($toSort)
            ->transform(function ($key, $value, $index) {
                return [$value[1][0], $value[1][1]];
            });
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

    /**
     * Wraps the given callback in a callback which has two arguments (value, key),
     * if the given callback accepts only one argument (assumed as value).
     * Allows for callbacks which expect exactly one argument like {@link ucfirst} or {@link is_string},
     * @param callable $callback
     * @return \Closure
     */
    protected static function wrapCallback(callable $callback)
    {
        $reflection = Callbacks::createReflectionFunction($callback);
        $numberOfParameters = $reflection->getNumberOfParameters();

        $proxy = $callback;

        if ($numberOfParameters == 1) {
            /** @noinspection PhpUnusedParameterInspection */
            /** @noinspection PhpDocSignatureInspection */
            $proxy = function ($value, $key) use ($callback) {
                return $callback($value);
            };
        }

        return $proxy;
    }
}