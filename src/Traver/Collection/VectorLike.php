<?php


namespace Traver\Collection;


use Traversable;

trait VectorLike
{
    use PipeableLike;

    public function isVectorLike()
    {
        return true;
    }

    /**
     * Implements {@link Pipable::flatten}.
     * @param int $level
     * @return PipeableLike
     */
    public function flatten($level = -1)
    {
        $builder = $this->builder();
        $this->flattenRecursive($this->asTraversable(), $level, $builder);
        return $builder->build();
    }

    /**
     * @codeCoverageIgnore
     * @param array|Traversable $traversable
     * @param $level
     * @param Builder $builder
     */
    private function flattenRecursive($traversable, $level, &$builder)
    {
        if ($level == 0) {
            $builder->addAll($traversable, false);
            return;
        }

        foreach ($traversable as $value) {
            if ($value instanceof Traversable || is_array($value)) {
                $this->flattenRecursive($value, $level - 1, $builder);
            } else {
                $builder->add($value);
            }
        }
    }
}