<?php


namespace Traver\Callback;


final class Comparators
{
    private function __construct()
    {
    }

    /**
     * @return \Closure
     */
    public static function naturalComparator()
    {
        return function ($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        };
    }

    private function __clone()
    {
    }
}