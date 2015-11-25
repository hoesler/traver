<?php
/**
 * Shopware Plugin HiltesImportExport
 * Copyright (C) 2015 Christoph Hösler
 */

namespace Traver\Iterator;


use CallbackFilterIterator;
use Iterator;

class CallbackOffsetIterator extends CallbackFilterIterator
{
    /**
     * @var bool
     */
    private $check;

    /**
     * DropWhileIterator constructor.
     * @param Iterator $iterator
     * @param callable $callback
     */
    function __construct(Iterator $iterator, callable $callback)
    {
        parent::__construct($iterator, $callback);
        $this->check = true;
    }

    /**
     * @inheritDoc
     */
    public function accept()
    {
        if ($this->check) {
            $accepted = parent::accept();
            if (!$accepted) {
                $this->check = false;
            }
            return !$accepted;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        parent::rewind();
        $this->check = false;
    }
}