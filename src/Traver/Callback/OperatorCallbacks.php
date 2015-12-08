<?php


namespace Traver\Callback;


use PhpOption\Option;

class OperatorCallbacks
{
    public static function add($a, $b)
    {
        return $a + $b;
    }

    public static function sub($a, $b)
    {
        return $a - $b;
    }

    /**
     * @param string $operator
     * @return Option Option of a callable.
     */
    public static function forOperator($operator)
    {
        $fun = null;
        switch ($operator) {
            case "+":
                $fun = [OperatorCallbacks::class, 'add'];
                break;
            case "-":
                $fun = [OperatorCallbacks::class, 'sub'];
                break;
        }
        return Option::fromValue($fun);
    }
}