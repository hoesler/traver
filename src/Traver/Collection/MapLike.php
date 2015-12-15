<?php


namespace Traver\Collection;


trait MapLike
{
    use PipeableLike;

    public function isVectorLike()
    {
        return false;
    }
}