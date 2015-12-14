<?php


namespace Traver\Test\UnitTest\Collection;


use Traver\Collection\Builder;
use Traver\Collection\FromArrayBuilder;
use Traver\Collection\ImmutableVector;
use Traver\Collection\PipeableView;

class VectorViewTest extends AbstractVectorTest
{
    /**
     * @return \Traver\Collection\Builder
     */
    protected function createBuilder()
    {
        return new VectorViewBuilder();
    }
}

class VectorViewBuilder implements Builder
{
    use FromArrayBuilder;

    public function build()
    {
        return new PipeableView(ImmutableVector::fromArray($this->array));
    }
}