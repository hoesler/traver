<?php


namespace Traver\Enumerable;


class ArrayObjectEnumerable extends \ArrayObject implements Enumerable, BuilderFactory
{
    use EnumerableLike;

    /**
     * @param array $input
     */
    public function __construct($input = [])
    {
        parent::__construct($input);
    }

    /**
     * @inheritDoc
     */
    public function builder()
    {
        return new ArrayObjectEnumerableBuilder();
    }
}

class ArrayObjectEnumerableBuilder implements Builder
{
    use FromArrayBuilder;

    /**
     * @return Enumerable
     */
    public function build()
    {
        return new ArrayObjectEnumerable($this->array);
    }
}