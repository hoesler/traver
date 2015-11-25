<?php

namespace Traver\Enumerable;


use ArrayIterator;
use Iterator;

class ArrayEnumerable extends AbstractEnumerable
{
    /**
     * @var Iterator
     */
    private $array;

    /**
     * MappingEnumerable constructor.
     * @param array $delegate
     */
    public function __construct(array $delegate)
    {
        $this->array = $delegate;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->array);
    }

    /**
     * @param Iterator $iterator
     * @return Enumerable
     */
    protected function forIterator(Iterator $iterator)
    {
        return new self(iterator_to_array($iterator));
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->array;
    }


}