<?php


namespace Traver\Collection;


interface Vector extends Collection
{
    /**
     * @param $level
     * @return Pipeable
     */
    public function flatten($level = -1);
}