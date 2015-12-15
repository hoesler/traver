<?php


namespace Traver\Collection;


class MutableMap implements \IteratorAggregate, Map
{
    use MapLike;
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

    public function builder()
    {
        return new MutableMapBuilder();
    }

    public function getIterator()
    {
        return $this->delegate->getIterator();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function delegate()
    {
        return $this->delegate;
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