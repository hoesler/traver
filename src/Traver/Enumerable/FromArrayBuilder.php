<?php


namespace Traver\Enumerable;


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
     * @param array $array
     */
    public function addAll($array)
    {
        foreach ($array as $key => $value) {
            $this->add($key, $value);
        }
    }
}