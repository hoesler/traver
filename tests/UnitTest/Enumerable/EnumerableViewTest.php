<?php

namespace Traver\Test\UnitTest\Enumerable;


use PHPUnit_Framework_TestCase;
use Traver\Enumerable\Builder;
use Traver\Enumerable\EnumerableView;
use Traver\Enumerable\FromArrayBuilder;

/**
 * @coversDefaultClass \Traver\Enumerable\EnumerableView
 */
class EnumerableViewTest extends PHPUnit_Framework_TestCase
{
    use EnumerableTest;

    /**
     * @return \Traver\Enumerable\Builder
     */
    protected function createBuilder()
    {
        return new ArrayObjectViewBuilder();
    }
}

class ArrayObjectViewBuilder implements Builder
{
    use FromArrayBuilder;

    /**
     * @inheritDoc
     */
    public function build()
    {
        $delegate = new \ArrayObject($this->array);
        return new EnumerableView($delegate);
    }
}