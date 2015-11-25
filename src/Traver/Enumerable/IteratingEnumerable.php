<?php

namespace Traver\Enumerable;


use Iterator;

class IteratingEnumerable extends AbstractEnumerable
{
    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * MappingEnumerable constructor.
     * @param Iterator $delegate
     */
    public function __construct(Iterator $delegate)
    {
        $this->iterator = $delegate;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    /**
     * @param Iterator $iterator
     * @return Enumerable
     */
    protected function forIterator(Iterator $iterator)
    {
        return new self($iterator);
    }
}