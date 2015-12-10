<?php


namespace Traver\Collection;


use Traversable;

trait FromArrayBuilder
{
    /**
     * @var array
     */
    private $array = [];

    /**
     * @param array|Traversable $array
     * @param bool $preserveKeys
     * @return Builder
     */
    public function addAll($array, $preserveKeys = true)
    {
        foreach ($array as $key => $value) {
            $this->add($value, ($preserveKeys) ? $key : null);
        }
        return $this;
    }

    /**
     * @param mixed $element
     * @param mixed $key
     * @return Builder
     */
    public function add($element, $key = null)
    {
        if (is_null($key)) {
            $this->array[] = $element;
        } else {
            $this->array[$key] = $element;
        }
        return $this;
    }
}