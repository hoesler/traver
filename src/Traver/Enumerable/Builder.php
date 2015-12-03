<?php


namespace Traver\Enumerable;


use Traversable;

/**
 * Interface Builder
 * @package Traver\Enumerable
 */
interface Builder
{
    /**
     * @param mixed $element
     * @param mixed $key
     * @return Builder
     */
    public function add($element, $key = null);

    /**
     * @param array|Traversable $array
     * @param bool $preserveKeys
     * @return Builder
     */
    public function addAll($array, $preserveKeys = true);

    /**
     * @return Enumerable
     */
    public function build();
}