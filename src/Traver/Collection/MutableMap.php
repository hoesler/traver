<?php


namespace Traver\Collection;


class MutableMap implements \IteratorAggregate, Map
{
    use PipeableLike;
    use ForwardingArrayAccess;

    /**
     * @var \ArrayObject
     */
    private $delegate;

    /**
     * @param array $input
     */
    public function __construct($input = [])
    {
        $this->delegate = new \ArrayObject($input);
    }

    /**
     * @inheritDoc
     */
    public function builder()
    {
        return new MutableMapBuilder();
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this->delegate->getIterator();
    }

    /**
     * @inheritDoc
     */
    function delegate()
    {
        return $this->delegate;
    }

    public function isVectorLike()
    {
        return false;
    }
}

class MutableMapBuilder implements Builder
{
    use FromArrayBuilder;

    /**
     * @return Pipeable
     */
    public function build()
    {
        return new MutableMap($this->array);
    }
}