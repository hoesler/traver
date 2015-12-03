<?php
/**
 * Shopware Plugin HiltesImportExport
 * Copyright (C) 2015 Christoph Hösler
 */

namespace Traver\Iterator;


use CallbackFilterIterator;
use Iterator;

class CallbackLimitIterator extends CallbackFilterIterator
{
    /**
     * @var bool
     */
    private $stop;

    /**
     * DropWhileIterator constructor.
     * @param Iterator $iterator
     * @param callable $callback
     */
    function __construct(Iterator $iterator, callable $callback)
    {
        parent::__construct($iterator, $callback);
        $this->stop = false;
    }

    public function accept()
    {
        if ($this->stop === true) {
            return false;
        }

        $accepted = parent::accept();
        if (!$accepted) {
            $this->stop = true;
        }
        return $accepted;
    }

    public function rewind()
    {
        parent::rewind();
        $this->stop = false;
    }
}