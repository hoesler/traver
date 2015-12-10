<?php


namespace Traver\Collection;


use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use Traver\Callback\Callbacks;
use Traver\Callback\Comparators;
use Traver\Exception\NoSuchElementException;
use Traver\Exception\UnsupportedOperationException;
use Traversable;

/**
 * Class PipeableLike
 * @package Traver\Collection
 * @implements Pipeable
 */
trait PipeableLike
{
    /**
     * Implements {@link Pipeable::head}.
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
     * Implements {@link Pipeable::asTraversable}.
     * @return Traversable
     */
    abstract public function asTraversable();

    /**
     * Implements {@link Pipeable::tail}.
     * @return PipeableLike
     */
    public function tail()
    {
        if ($this->isEmpty()) {
            throw new UnsupportedOperationException();
        }
        return $this->drop(1);
    }

    /**
     * Implements {@link Pipeable::isEmpty}.
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
     * Implements {@link Pipeable::drop}.
     * @param $n
     * @return PipeableLike
     */
    public function drop($n)
    {
        return $this->slice($n, PHP_INT_MAX);
    }

    /**
     * Implements {@link Pipeable::slice}.
     * @param int $from
     * @param int $until
     * @return PipeableLike
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
     * Creates a new Builder for the current class implementing Pipeable.
     * @return Builder
     */
    abstract protected function builder();

    /**
     * Implements {@link Pipeable::count}.
     * @return int
     */
    public function count()
    {
        return iterator_count($this->asTraversable());
    }

    /**
     * Implements {@link Pipeable::countWhich}.
     * @param callable $predicate
     * @return int
     */
    public function countWhich(callable $predicate)
    {
        $predicate = self::wrapCallback($predicate);
        return iterator_count($this->select($predicate)->asTraversable());
    }

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

    /**
     * Implements {@link Pipeable::select}.
     * @param callable $predicate
     * @return PipeableLike
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
     * Implements {@link Pipeable::dropWhile}.
     * @param callable $predicate
     * @return PipeableLike
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
     * Implements {@link Pipeable::take}.
     * @param int $n
     * @return PipeableLike
     */
    public function take($n)
    {
        return $this->slice(0, $n);
    }

    /**
     * Implements {@link Pipeable::takeWhile}.
     * @param callable $predicate
     * @return PipeableLike
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
     * Implements {@link Pipeable::reject}.
     * @param callable $predicate
     * @return PipeableLike
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
     * Implements {@link Pipeable::find}.
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
     * Implements {@link Pipeable::flatMap}.
     * @param callable $mappingFunction
     * @return PipeableLike
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
     * Implements {@link Pipeable::all}.
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
     * Implements {@link Pipeable::any}.
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
     * @return PipeableLike
     * @see Pipeable::groupBy
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

        $builder = ImmutableMap::newBuilder();
        foreach ($result as $key => $group) {
            $groupPipeable = $this->newCollection($group);
            $builder->add($groupPipeable, $key);
        }
        return $builder->build();
    }

    /**
     * @param $array |Traversable
     * @return Pipeable
     */
    protected final function newCollection($array)
    {
        return $this->builder()->addAll($array)->build();
    }

    /**
     * Concatenates the {@link strval} of all elements separated by the given separator.
     * @param $separator
     * @return mixed
     * @see Pipeable::join
     */
    public function join($separator = '')
    {
        return $this->reduceOption(function ($joined, $item) use ($separator) {
            return $joined . $separator . $item;
        })->getOrElse('');
    }

    /**
     * Implements {@link Pipeable::reduceOption}.
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
     * Implements {@link Pipeable::reduce}.
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
     * Implements {@link Pipeable::keys}.
     * @return PipeableLike
     */
    public function keys()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return $this->transform(function ($key, $value, $index) {
            return [$index, $key];
        });
    }

    /**
     * @param callable $transformationFunction
     * @return PipeableLike
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
     * Implements {@link Pipeable::values}.
     * @return PipeableLike
     */
    public function values()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return $this->transform(function ($key, $value, $index) {
            return [$index, $value];
        });
    }

    /**
     * Implements {@link Pipeable::entries}.
     * @return PipeableLike
     */
    public function entries()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return $this->transform(function ($key, $value, $index) {
            return [$index, [$key, $value]];
        });
    }

    /**
     * Implements {@link Pipeable::each}.
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
     * Implements {@link Pipeable::sortBy}.
     * @param callable $mappingFunction
     * @return PipeableLike
     */
    public function sortBy(callable $mappingFunction)
    {
        $mappingFunction = self::wrapCallback($mappingFunction);
        $sortedKeyEntryPairs = $this
            ->map(function ($value, $key) use ($mappingFunction) {
                return [$mappingFunction($value, $key), [$key, $value]];
            })
            ->sort();

        $builder = $this->builder();
        foreach ($sortedKeyEntryPairs as $keyEntryPair) {
            list($key, $value) = $keyEntryPair[1];
            $builder->add($value, $key);
        }
        return $builder->build();
    }

    /**
     * Implements {@link Pipeable::sort}.
     * @param callable|null $compareFunction
     * @return PipeableLike
     */
    public function sort(callable $compareFunction = null)
    {
        if ($compareFunction === null) {
            $compareFunction = Comparators::naturalComparator();
        }
        $array = $this->toArray();
        $success = uasort($array, $compareFunction);
        if ($success === false) {
            throw new \RuntimeException("sort failed");
        }

        return $this->newCollection($array);
    }

    /**
     * Implements {@link Pipeable::toArray}.
     * @param bool $preserveKeys
     * @return array
     */
    public function toArray($preserveKeys = true)
    {
        return iterator_to_array($this->asTraversable(), $preserveKeys);
    }

    /**
     * Implements {@link Pipeable::map}.
     * @param callable $mappingFunction
     * @return PipeableLike
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
     * @return Pipeable
     */
    protected final function mutableCopy()
    {
        if ($this->isVectorLike()) {
            return new MutableVector($this->toArray());
        } else {
            return new MutableMap($this->toArray());
        }
    }

    /**
     * Tests if the collection is vector like. The result is used to control preservation of keys.
     * @return mixed
     */
    abstract public function isVectorLike();
}