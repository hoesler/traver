<?php

namespace Traver\Enumerable;


use CallbackFilterIterator;
use Iterator;
use LimitIterator;
use precore\util\Iterators;
use precore\util\Predicates;
use Traver\Exception\NoSuchElementException;
use Traver\Iterator\CallbackOffsetIterator;
use Traver\Iterator\MappingIterator;

abstract class AbstractEnumerable implements Enumerable
{
    public function map(callable $mappingFunction)
    {
        return $this->forIterator(new MappingIterator($this->getIterator(), $mappingFunction));
    }

    /**
     * @inheritDoc
     */
    public function head()
    {
        $iterator = $this->getIterator();
        $iterator->rewind();
        if ($iterator->valid()) {
            return $iterator->current();
        } else {
            throw new NoSuchElementException();
        }
    }

    /**
     * @inheritDoc
     */
    public function tail()
    {
        return $this->forIterator(new LimitIterator($this->getIterator(), 1));
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return iterator_to_array($this);
    }

    /**
     * @inheritDoc
     */
    public function aggregate(callable $binaryFunction, $initialValue = null)
    {
        $result = $initialValue;
        foreach ($this as $key => $value) {
            $result = $binaryFunction($result, $value, $key);
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return iterator_count($this);
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
        $iterator = new LimitIterator($this->getIterator(), $n);
        return $this->forIterator($iterator);
    }

    /**
     * @inheritDoc
     */
    public function dropWhile(callable $predicate)
    {
        return $this->forIterator(new CallbackOffsetIterator($this->getIterator(), $predicate));
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
        return $this->forIterator(new CallbackFilterIterator($this->getIterator(), $predicate));
    }

    /**
     * @inheritDoc
     */
    public function filterNot(callable $predicate)
    {
        return $this->forIterator(Iterators::filter($this->getIterator(), Predicates::not($predicate)));
    }

    /**
     * @param Iterator $iterator
     * @return Enumerable
     */
    abstract protected function forIterator(Iterator $iterator);

    /**
     * @inheritDoc
     * @return Iterator
     */
    abstract public function getIterator();
}