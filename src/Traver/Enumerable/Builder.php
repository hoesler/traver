<?php


namespace Traver\Enumerable;


use Traversable;

interface Builder
{
    /**
     * @param mixed $key
     * @param mixed $element
     * @return Builder
     */
    public function add($key, $element);

    /**
     * @param array|Traversable $array
     * @param bool $preserveKeys
     * @return mixed
     */
    public function addAll($array, $preserveKeys = true);

    /**
     * @return Enumerable
     */
    public function build();
}