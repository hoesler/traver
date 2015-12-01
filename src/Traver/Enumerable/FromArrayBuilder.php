<?php


namespace Traver\Enumerable;


use Traversable;

trait FromArrayBuilder
{
    /**
     * @var array
     */
    private $array = [];

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

    /**
     * @param array|Traversable $array
     * @param bool $preserveKeys
     * @return Builder
     */
    public function addAll($array, $preserveKeys = true)
    {
        foreach ($array as $key => $value) {
            if ($preserveKeys) {
                $this->add($value, $key);
            } else {
                array_push($this->array, $value);
            }
        }
        return $this;
    }
}