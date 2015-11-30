<?php


namespace Traver\Test\UnitTest\Enumerable;


use PHPUnit_Framework_TestCase;
use Traver\Enumerable\ArrayObjectEnumerable;

/**
 * Class ArrayObjectEnumerableTest
 * @package Traver\Test\UnitTest\Enumerable
 * @coversDefaultClass \Traver\Enumerable\ArrayObjectEnumerable
 */
class ArrayObjectEnumerableTest extends PHPUnit_Framework_TestCase
{
    use EnumerableTest;

    /**
     * @return \Traver\Enumerable\Builder
     */
    protected function createBuilder()
    {
        $arrayObjectEnumerable = new ArrayObjectEnumerable();
        return $arrayObjectEnumerable->builder();
    }
}