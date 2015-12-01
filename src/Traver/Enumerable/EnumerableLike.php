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
            $builder->add($key, $mappingFunction($element));
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
    public function aggregate(callable $binaryFunction, $initialValue = null)
    {
        $result = $initialValue;
        foreach ($this->asTraversable() as $key => $value) {
            $result = $binaryFunction($result, $value, $key);
        }
        return $result;
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
                    $builder->add($key, $element);
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
                $builder->add($key, $element);
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
                $builder->add($key, $element);
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
            $builder->addAll($array, false);
        }
        return $builder->build();
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