<?php


namespace Traver\Test\UnitTest\Collection;


use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;
use Traver\Collection\Pipeable;
use Traver\Collection\PipeableView;
use Traversable;

abstract class AbstractPipeableTest extends PHPUnit_Framework_TestCase
{
    use BuildableCollectionTest;

    /**
     * @dataProvider mapProvider
     * @covers ::map
     * @param $array
     * @param $mappingFunction
     * @param $expected
     */
    public function testMap($array, $mappingFunction, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $mapped = $enumerable->map($mappingFunction);

        // then
        self::assertInstanceOf(Pipeable::class, $mapped);
        self::assertEquals($expected, iterator_to_array($mapped));
    }

    abstract public function mapProvider();

    /**
     * @covers ::isEmpty
     * @dataProvider emptyProvider
     * @param $array
     * @param $expected
     */
    public function testIsEmpty($array, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $isEmpty = $enumerable->isEmpty();

        // then
        self::assertEquals($expected, $isEmpty);
    }

    abstract public function emptyProvider();

    /**
     * @covers ::slice
     * @dataProvider sliceProvider
     * @param $input
     * @param $from
     * @param $until
     * @param $expected
     */
    public function testSlice($input, $from, $until, $expected)
    {
        // given
        $array = $input;
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $slice = $enumerable->slice($from, $until);

        // then
        $actual = iterator_to_array($slice, true);
        self::assertEquals($expected, $actual);
    }

    abstract public function sliceProvider();

    /**
     * @dataProvider toArrayProvider
     * @covers ::toArray
     * @param $array
     * @param $expected
     * @param $args
     */
    public function testToArray($array, $expected, ...$args)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $toArray = $enumerable->toArray(...$args);

        // then
        self::assertEquals($expected, $toArray);
    }

    abstract public function toArrayProvider();

    /**
     * @covers ::takeWhile
     * @dataProvider takeWhileProvider
     * @param $array
     * @param $predicate
     * @param $expected
     */
    public function testTakeWhile($array, $predicate, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $taken = $enumerable->takeWhile($predicate);

        // then
        self::assertInstanceOf(Pipeable::class, $taken);
        self::assertEquals($expected, iterator_to_array($taken));
    }

    abstract public function takeWhileProvider();

    /**
     * @covers ::select
     * @dataProvider selectProvider
     * @param $array
     * @param $predicate
     * @param $expected
     */
    public function testSelect($array, $predicate, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $filtered = $enumerable->select($predicate);

        // then
        self::assertInstanceOf(Pipeable::class, $filtered);
        self::assertEquals($expected, iterator_to_array($filtered));
    }

    abstract public function selectProvider();

    /**
     * @covers ::flatMap
     * @dataProvider flatMapProvider
     * @param array $array
     * @param callable $mappingFunction
     * @param array $expected
     */
    public function testFlatMap(array $array, callable $mappingFunction, array $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $var = $enumerable->flatMap($mappingFunction);

        // then
        self::assertEquals($expected, iterator_to_array($var));
    }

    abstract public function flatMapProvider();

    /**
     * @covers ::all
     * @dataProvider allProvider
     * @param array $array
     * @param $predicate
     * @param $expected
     */
    public function testAll(array $array, $predicate, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $all = $enumerable->all($predicate);

        // then
        self::assertEquals($expected, $all);
    }

    abstract public function allProvider();

    /**
     * @covers ::any
     * @dataProvider anyProvider
     * @param array $array
     * @param $predicate
     * @param $expected
     */
    public function testAny(array $array, $predicate, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $any = $enumerable->any($predicate);

        // then
        self::assertEquals($expected, $any);
    }

    abstract public function anyProvider();

    /**
     * @covers ::groupBy
     * @dataProvider groupByProvider
     * @param array $array
     * @param callable $keyFunction
     * @param array $expected
     */
    public function testGroupBy(array $array, $keyFunction, array $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $group = $enumerable->groupBy($keyFunction);

        // then
        self::assertInstanceOf(Pipeable::class, $group);
        self::assertEquals(array_keys($expected), array_keys(iterator_to_array($group)),
            "Group keys differ.");
        $allValuesAreEnumerables = array_reduce(array_values(iterator_to_array($group)), function ($all, $e) {
            return $all && ($e instanceof Pipeable);
        }, true);
        self::assertTrue($allValuesAreEnumerables);

        $actualAsArray = array_map(function ($e) {
            return iterator_to_array($e);
        }, iterator_to_array($group));
        self::assertEquals($expected, $actualAsArray);
    }

    abstract public function groupByProvider();

    /**
     * @covers ::reduce
     * @dataProvider reduceProvider
     * @param array $array
     * @param $args
     * @param $expected
     */
    public function testReduce(array $array, $expected, ...$args)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $reduced = $enumerable->reduce(...$args);

        // then
        self::assertEquals($expected, $reduced);
    }

    abstract public function reduceProvider();

    /**
     * @covers ::reduce
     * @expectedException \Traver\Exception\UnsupportedOperationException
     */
    public function testReduceEmpty()
    {
        // given
        $builder = $this->createBuilder();
        $enumerable = $builder->build();

        // when
        $enumerable->reduce(function () {
        });

        // then
        self::fail();
    }

    /**
     * @covers ::reduceOption
     * @dataProvider reduceOptionProvider
     * @param array $array
     * @param $expected
     * @param $args
     */
    public function testReduceOption(array $array, $expected, ...$args)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $reduced = $enumerable->reduceOption(...$args);

        // then
        self::assertEquals($expected, $reduced);
    }

    abstract public function reduceOptionProvider();

    /**
     * @covers ::join
     * @dataProvider joinProvider
     * @param array $array
     * @param $expected
     * @param $args
     */
    public function testJoin($array, $expected, ...$args)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $joined = $enumerable->join(...$args);

        // then
        self::assertEquals($joined, $expected);
    }

    abstract public function joinProvider();

    /**
     * @covers ::keys
     * @dataProvider keysProvider
     * @param $array
     * @param $expected
     */
    public function testKeys($array, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $keys = $enumerable->keys();

        // then
        self::assertInstanceOf(Pipeable::class, $keys);
        self::assertEquals($expected, iterator_to_array($keys));
    }

    abstract public function keysProvider();

    /**
     * @covers ::values
     * @dataProvider valuesProvider
     * @param $array
     * @param $expected
     */
    public function testValues($array, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $values = $enumerable->values();

        // then
        self::assertInstanceOf(Pipeable::class, $values);
        self::assertEquals($expected, iterator_to_array($values));
    }

    abstract public function valuesProvider();

    /**
     * @covers ::entries
     * @dataProvider entriesProvider
     * @param $array
     * @param $expected
     */
    public function testEntries($array, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $entries = $enumerable->entries();

        // then
        self::assertInstanceOf(Pipeable::class, $entries);
        self::assertEquals($expected, iterator_to_array($entries));
    }

    abstract public function entriesProvider();

    /**
     * @covers ::sort
     * @dataProvider sortProvider
     * @param $array
     * @param $expected
     * @param $args
     */
    public function testSort($array, $expected, ...$args)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $sorted = $enumerable->sort(...$args);

        // then
        self::assertInstanceOf(Pipeable::class, $sorted);
        self::assertEquals($expected, iterator_to_array($sorted));
    }

    abstract public function sortProvider();

    /**
     * @covers ::sortBy
     * @dataProvider sortByProvider
     * @param $array
     * @param $callback
     * @param $expected
     */
    public function testSortBy($array, $callback, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $sorted = $enumerable->sortBy($callback);

        // then
        self::assertInstanceOf(Pipeable::class, $sorted);
        self::assertEquals($expected, iterator_to_array($sorted));
    }

    abstract public function sortByProvider();

    /**
     * @covers ::head
     * @expectedException \Traver\Exception\NoSuchElementException
     */
    public function testHeadEmpty()
    {
        // given
        $array = [];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $enumerable->head();

        // then
        PHPUnit_Framework_Assert::fail();
    }

    /**
     * @covers ::tail
     * @expectedException \Traver\Exception\UnsupportedOperationException
     */
    public function testTailEmpty()
    {
        // given
        $array = [];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $enumerable->tail();

        // then
        PHPUnit_Framework_Assert::fail("tail did not throw an UnsupportedOperationException on empty collection");
    }

    /**
     * @dataProvider rejectProvider
     * @covers ::reject
     * @param $array
     * @param $expected
     * @param $rejectArgs
     */
    public function testReject($array, $expected, ...$rejectArgs)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $filtered = $enumerable->reject(...$rejectArgs);

        // then
        PHPUnit_Framework_Assert::assertInstanceOf(Pipeable::class, $filtered);
        PHPUnit_Framework_Assert::assertEquals($expected, iterator_to_array($filtered));
    }

    abstract public function rejectProvider();

    /**
     * @dataProvider findProvider
     * @covers ::find
     * @param $array
     * @param $expected
     * @param $args
     */
    public function testFind($array, $expected, ...$args)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $var = $enumerable->find(...$args);

        // then
        PHPUnit_Framework_Assert::assertEquals($expected, $var);
    }

    abstract public function findProvider();

    /**
     * @dataProvider tailProvider
     * @covers ::tail
     * @param $array
     * @param $expected
     */
    public function testTail($array, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $tail = $enumerable->tail();

        // then
        PHPUnit_Framework_Assert::assertInstanceOf(Pipeable::class, $tail);
        PHPUnit_Framework_Assert::assertEquals($expected, iterator_to_array($tail));
    }

    abstract public function tailProvider();

    /**
     * @dataProvider dropProvider
     * @covers ::drop
     * @param $array
     * @param $expected
     * @param $n
     */
    public function testDrop($array, $expected, $n)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $drop = $enumerable->drop($n);

        // then
        PHPUnit_Framework_Assert::assertInstanceOf(Pipeable::class, $drop);
        PHPUnit_Framework_Assert::assertEquals($expected, iterator_to_array($drop));
    }

    abstract public function dropProvider();

    /**
     * @dataProvider dropWhileProvider
     * @covers ::dropWhile
     * @param $array
     * @param $expected
     * @param $predicate
     */
    public function testDropWhile($array, $expected, $predicate)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $dropWhile = $enumerable->dropWhile($predicate);

        // then
        PHPUnit_Framework_Assert::assertInstanceOf(Pipeable::class, $dropWhile);
        PHPUnit_Framework_Assert::assertEquals($expected, iterator_to_array($dropWhile));
    }

    abstract public function dropWhileProvider();

    /**
     * @dataProvider takeProvider
     * @covers ::take
     * @param $array
     * @param $n
     * @param $expected
     */
    public function testTake($array, $n, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $taken = $enumerable->take($n);

        // then
        PHPUnit_Framework_Assert::assertInstanceOf(Pipeable::class, $taken);
        PHPUnit_Framework_Assert::assertEquals($expected, iterator_to_array($taken));
    }

    abstract public function takeProvider();

    /**
     * @dataProvider eachProvider
     * @covers ::each
     * @param $array
     */
    public function testEach($array)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();
        $copy = [];

        // when
        $enumerable->each(function ($value, $key) use (&$copy) {
            $copy[$key] = $value;
        });

        // then
        PHPUnit_Framework_Assert::assertEquals($array, $copy);
    }

    abstract public function eachProvider();

    /**
     * @dataProvider countProvider
     * @covers ::count
     * @param $array
     * @param $expected
     */
    public function testCount($array, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $count = $enumerable->count();

        // then
        PHPUnit_Framework_Assert::assertEquals($expected, $count);
    }

    abstract public function countProvider();

    /**
     * @dataProvider countWhichProvider
     * @covers ::countWhich
     * @param $array
     * @param $expected
     * @param $predicate
     */
    public function testCountWhich($array, $expected, $predicate)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $count = $enumerable->countWhich($predicate);

        // then
        PHPUnit_Framework_Assert::assertEquals($expected, $count);
    }

    abstract public function countWhichProvider();

    /**
     * @dataProvider asTraversableProvider
     * @covers ::asTraversable
     * @param $array
     */
    public function testAsTraversable($array)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $traversable = $enumerable->asTraversable();

        // then
        PHPUnit_Framework_Assert::assertInstanceOf(Traversable::class, $traversable);
        PHPUnit_Framework_Assert::assertEquals($array, iterator_to_array($traversable));
    }

    abstract public function asTraversableProvider();

    /**
     * @covers ::getIterator
     */
    public function testGetIterator()
    {
        // given
        $array = [1, 2, 3];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $iterator = $enumerable->getIterator();

        // then
        self::assertInstanceOf(\Iterator::class, $iterator);
        self::assertEquals($array, iterator_to_array($iterator));
    }

    /**
     * @covers ::view
     */
    public function testView()
    {
        // given
        $builder = $this->createBuilder();
        $enumerable = $builder->build();

        // when
        $view = $enumerable->view();

        // then
        self::assertInstanceOf(PipeableView::class, $view);
    }

    /**
     * @covers ::force
     */
    public function testForce()
    {
        // given
        $builder = $this->createBuilder();
        $enumerable = $builder->build();

        // when
        $force = $enumerable->force();

        // then
        self::assertNotInstanceOf(PipeableView::class, $force);
    }
}