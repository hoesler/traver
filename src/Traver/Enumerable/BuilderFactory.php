<?php


namespace Traver\Enumerable;


interface BuilderFactory
{
    /**
     * @return Builder
     */
    public function builder();
}