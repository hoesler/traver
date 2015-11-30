<?php


namespace Traver\Enumerable;


interface Builder
{
    /**
     * @param mixed $key
     * @param mixed $element
     * @return Builder
     */
    public function add($key, $element);

    public function addAll($array);

    /**
     * @return Enumerable
     */
    public function build();
}