<?php


namespace Traver\Test\UnitTest\Enumerable;


use PHPUnit_Framework_Assert;
use PHPUnit_Framework_TestCase;
use Traver\Enumerable\Enumerable;
use Traversable;

abstract class AbstractEnumerableTest extends PHPUnit_Framework_TestCase
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
        self::assertInstanceOf(Enumerable::class, $mapped);
        self::assertEquals($expected, iterator_to_array($mapped));
    }

    public abstract function mapProvider();

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

    public abstract function emptyProvider();

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

    public abstract function sliceProvider();

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

    public abstract function toArrayProvider();

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
        self::assertInstanceOf(Enumerable::class, $taken);
        self::assertEquals($expected, iterator_to_array($taken));
    }

    public abstract function takeWhileProvider();

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
        self::assertInstanceOf(Enumerable::class, $filtered);
        self::assertEquals($expected, iterator_to_array($filtered));
    }

    public abstract function selectProvider();

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

    public abstract function flatMapProvider();

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

    public abstract function allProvider();

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

    public abstract function anyProvider();

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
        self::assertInstanceOf(Enumerable::class, $group);
        self::assertEquals(array_keys($expected), array_keys(iterator_to_array($group)),
            "Group keys differ.");
        $allValuesAreEnumerables = array_reduce(array_values(iterator_to_array($group)), function ($all, $e) {
            return $all && ($e instanceof Enumerable);
        }, true);
        self::assertTrue($allValuesAreEnumerables);

        $actualAsArray = array_map(function ($e) {
            return iterator_to_array($e);
        }, iterator_to_array($group));
        self::assertEquals($expected, $actualAsArray);
    }

    public abstract function groupByProvider();

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

    public abstract function reduceProvider();

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

    public abstract function reduceOptionProvider();

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

    public abstract function joinProvider();

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
        self::assertInstanceOf(Enumerable::class, $keys);
        self::assertEquals($expected, iterator_to_array($keys));
    }

    public abstract function keysProvider();

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
        self::assertInstanceOf(Enumerable::class, $values);
        self::assertEquals($expected, iterator_to_array($values));
    }

    public abstract function valuesProvider();

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
        self::assertInstanceOf(Enumerable::class, $entries);
        self::assertEquals($expected, iterator_to_array($entries));
    }

    public abstract function entriesProvider();

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
        self::assertInstanceOf(Enumerable::class, $sorted);
        self::assertEquals($expected, iterator_to_array($sorted));
    }

    public abstract function sortProvider();

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
        self::assertInstanceOf(Enumerable::class, $sorted);
        self::assertEquals($expected, iterator_to_array($sorted));
    }

    public abstract function sortByProvider();

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
        PHPUnit_Framework_Assert::assertInstanceOf(Enumerable::class, $filtered);
        PHPUnit_Framework_Assert::assertEquals($expected, iterator_to_array($filtered));
    }

    public abstract function rejectProvider();

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

    public abstract function findProvider();

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
        PHPUnit_Framework_Assert::assertInstanceOf(Enumerable::class, $tail);
        PHPUnit_Framework_Assert::assertEquals($expected, iterator_to_array($tail));
    }

    public abstract function tailProvider();

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
        PHPUnit_Framework_Assert::assertInstanceOf(Enumerable::class, $drop);
        PHPUnit_Framework_Assert::assertEquals($expected, iterator_to_array($drop));
    }

    public abstract function dropProvider();

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
        PHPUnit_Framework_Assert::assertInstanceOf(Enumerable::class, $dropWhile);
        PHPUnit_Framework_Assert::assertEquals($expected, iterator_to_array($dropWhile));
    }

    public abstract function dropWhileProvider();

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
        PHPUnit_Framework_Assert::assertInstanceOf(Enumerable::class, $taken);
        PHPUnit_Framework_Assert::assertEquals($expected, iterator_to_array($taken));
    }

    public abstract function takeProvider();

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

    public abstract function eachProvider();

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

    public abstract function countProvider();

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

    public abstract function countWhichProvider();

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

    public abstract function asTraversableProvider();
}