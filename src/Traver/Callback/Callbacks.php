<?php


namespace Traver\Callback;


final class Callbacks
{
    private function __construct()
    {
    }

    /**
     * @param callable $f
     * @return \ReflectionFunction|\ReflectionMethod
     */
    public static function createReflectionFunction(callable $f)
    {
        if ($f instanceof \Closure) { // Closure.
            $reflection = new \ReflectionFunction($f);
            return $reflection;
        } elseif (is_array($f)) { // Methods passed as an array.
            $reflection = new \ReflectionMethod($f[0], $f[1]);
            return $reflection;
        } elseif (is_object($f)) { // Callable objects.
            $reflection = new \ReflectionMethod($f, '__invoke');
            return $reflection;
        } else { // Everything else (method names delimited by :: do not work with $callable() syntax before PHP 7).
            $reflection = new \ReflectionFunction($f);
            return $reflection;
        }
    }

    private function __clone()
    {
    }
}