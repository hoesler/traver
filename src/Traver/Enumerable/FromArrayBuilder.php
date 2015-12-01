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
     * @param mixed $key
     * @param mixed $element
     * @return Builder
     */
    public function add($key, $element)
    {
        $this->array[$key] = $element;
    }

    /**
     * @param array|Traversable $array
     * @param bool $preserveKeys
     */
    public function addAll($array, $preserveKeys = true)
    {
        foreach ($array as $key => $value) {
            if ($preserveKeys) {
                $this->add($key, $value);
            } else {
                array_push($this->array, $value);
            }
        }
    }
}