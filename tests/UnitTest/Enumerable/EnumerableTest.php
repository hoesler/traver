<?php


namespace Traver\Test\UnitTest\Enumerable;


use PhpOption\None;
use PhpOption\Some;
use PHPUnit_Framework_TestCase;
use Traver\Enumerable\Enumerable;
use Traversable;

/**
 * Class EnumerableTest
 * @package Traver\Test\UnitTest\Enumerable
 * @coversDefaultClass \Traver\Enumerable\Enumerable
 */
trait EnumerableTest
{
    /**
     * @covers ::asTraversable
     */
    public function testAsTraversable()
    {
        // given
        $expected = ["a", "b", "c"];
        $builder = $this->createBuilder();
        $builder->addAll($expected);
        $enumerable = $builder->build();

        // when
        $iterable2 = $enumerable->asTraversable();

        // then
        PHPUnit_Framework_TestCase::assertInstanceOf(Traversable::class, $iterable2);
        PHPUnit_Framework_TestCase::assertEquals($expected, iterator_to_array($iterable2));
    }

    /**
     * @covers ::map
     */
    public function testMap()
    {
        // given
        $array = ["a", "b", "c"];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        $mappingFunction = function ($item) {
            return $item . "_";
        };

        // when
        $mapped = $enumerable->map($mappingFunction);

        // then
        PHPUnit_Framework_TestCase::assertInstanceOf(Enumerable::class, $mapped);
        PHPUnit_Framework_TestCase::assertEquals(array_map($mappingFunction, $array), iterator_to_array($mapped));
    }

    /**
     * @covers ::head
     */
    public function testHead()
    {
        // given
        $array = ["a", "b", "c"];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $head = $enumerable->head();

        // then
        PHPUnit_Framework_TestCase::assertEquals("a", $head);
    }

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
        PHPUnit_Framework_TestCase::fail();
    }

    /**
     * @covers ::tail
     */
    public function testTail()
    {
        // given
        $array = ["a", "b", "c"];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $tail = $enumerable->tail();

        // then
        PHPUnit_Framework_TestCase::assertInstanceOf(Enumerable::class, $tail);
        PHPUnit_Framework_TestCase::assertEquals(array_slice($array, 1, null, true), iterator_to_array($tail));
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
        PHPUnit_Framework_TestCase::fail("tail did not throw an UnsupportedOperationException on empty collection");
    }

    /**
     * @covers ::isEmpty
     */
    public function testIsEmptyTrue()
    {
        // given
        $array = [];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $isEmpty = $enumerable->isEmpty();

        // then
        PHPUnit_Framework_TestCase::assertTrue($isEmpty);
    }

    /**
     * @covers ::isEmpty
     */
    public function testIsEmptyFalse()
    {
        // given
        $array = ["a"];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $isEmpty = $enumerable->isEmpty();

        // then
        PHPUnit_Framework_TestCase::assertFalse($isEmpty);
    }

    /**
     * @covers ::slice
     * @dataProvider sliceProvider
     * @param $input
     * @param $from
     * @param $until
     */
    public function testSlice($input, $from, $until)
    {
        // given
        $array = $input;
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $slice = $enumerable->slice($from, $until);

        // then
        $expected = array_slice($array, $from, max($until - $from, 0), true);
        $actual = iterator_to_array($slice, true);
        PHPUnit_Framework_TestCase::assertEquals($expected, $actual);
    }

    public function sliceProvider()
    {
        return [
            [["a", "b", "c"], 0, 0],
            [["a", "b", "c"], 0, 3],
            [["a", "b", "c"], 0, 1],
            [["a", "b", "c"], 1, 2],
            [["a", "b", "c"], 2, 1],
            [["a", "b", "c"], 2, 23]
        ];
    }

    /**
     * @covers ::each
     */
    public function testEach()
    {
        // given
        $array = ["a", "b", "c"];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();
        $newArray = [];

        // when
        $enumerable->each(function ($value, $key) use (&$newArray) {
            $newArray[$key] = $value;
        });

        // then
        PHPUnit_Framework_TestCase::assertEquals($array, $newArray);
    }

    /**
     * @covers ::toArray
     */
    public function testToArray()
    {
        // given
        $array = ["a", "b", "foo" => "c"];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $toArray = $enumerable->toArray(true);

        // then
        PHPUnit_Framework_TestCase::assertEquals($array, $toArray);
    }

    /**
     * @covers ::count
     */
    public function testCount()
    {
        // given
        $array = [10, 5, 1];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $count = $enumerable->count();

        // then
        PHPUnit_Framework_TestCase::assertEquals(3, $count);
    }

    /**
     * @covers ::countWhich
     */
    public function testCountWhich()
    {
        // given
        $array = [10, 5, 1];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $count = $enumerable->countWhich(function ($el) {
            return $el < 10;
        });

        // then
        PHPUnit_Framework_TestCase::assertEquals(2, $count);
    }

    /**
     * @covers ::drop
     */
    public function testDrop()
    {
        // given
        $array = [10, 5, 1];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $drop = $enumerable->drop(2);

        // then
        PHPUnit_Framework_TestCase::assertInstanceOf(Enumerable::class, $drop);
        PHPUnit_Framework_TestCase::assertEquals(array_slice($array, 2, null, true), iterator_to_array($drop));
    }

    /**
     * @covers ::dropWhile
     */
    public function testDropWhile()
    {
        // given
        $array = [10, 5, 1];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();
        $predicate = function ($el) {
            return $el > 5;
        };

        // when
        $dropWhile = $enumerable->dropWhile($predicate);

        // then
        PHPUnit_Framework_TestCase::assertInstanceOf(Enumerable::class, $dropWhile);
        PHPUnit_Framework_TestCase::assertEquals(array_slice($array, 1, null, true), iterator_to_array($dropWhile));
    }

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
        PHPUnit_Framework_TestCase::assertInstanceOf(Enumerable::class, $taken);
        PHPUnit_Framework_TestCase::assertEquals($expected, iterator_to_array($taken));
    }

    public function takeProvider()
    {
        return [
            [[10, 5, 1], 2, [10, 5]],
            [[10, 5, 1], 0, []],
            [[], 2, []]
        ];
    }

    /**
     * @covers ::dropWhile
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
        PHPUnit_Framework_TestCase::assertInstanceOf(Enumerable::class, $taken);
        PHPUnit_Framework_TestCase::assertEquals($expected, iterator_to_array($taken));
    }

    public function takeWhileProvider()
    {
        return [
            [[10, 5, 1], function ($el) {
                return $el >= 5;
            }, [10, 5]],
            [[10, 5, 1], function ($el) {
                return $el <= 5;
            }, []],
            [[], function ($el) {
                return true;
            }, []]
        ];
    }

    /**
     * @covers ::exists
     */
    public function testExists()
    {
        // given
        $array = [10, 5, 1];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();
        $predicate = function ($el) {
            return $el > 5;
        };

        // when
        $exists = $enumerable->exists($predicate);

        // then
        PHPUnit_Framework_TestCase::assertTrue($exists);
    }

    /**
     * @covers ::exists
     */
    public function testExistsNot()
    {
        // given
        $array = [10, 5, 1];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();
        $predicate = function ($el) {
            return $el > 10;
        };

        // when
        $exists = $enumerable->exists($predicate);

        // then
        PHPUnit_Framework_TestCase::assertFalse($exists);
    }

    /**
     * @covers ::filter
     */
    public function testFilter()
    {
        // given
        $array = ["a", "b", "c"];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();
        $predicate = function ($el) {
            return $el != "b";
        };

        // when
        $filtered = $enumerable->select($predicate);

        // then
        PHPUnit_Framework_TestCase::assertInstanceOf(Enumerable::class, $filtered);
        PHPUnit_Framework_TestCase::assertEquals(array_filter($array, $predicate), iterator_to_array($filtered));
    }

    /**
     * @covers ::reject
     */
    public function testReject()
    {
        // given
        $array = ["a", "b", "c"];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();
        $predicate = function ($el) {
            return $el == "b";
        };

        // when
        $filtered = $enumerable->reject($predicate);

        // then
        PHPUnit_Framework_TestCase::assertInstanceOf(Enumerable::class, $filtered);
        $actual = iterator_to_array($filtered);
        $expected = array_filter($array, function ($value, $key) use ($predicate) {
            return !$predicate($value, $key);
        }, ARRAY_FILTER_USE_BOTH);
        PHPUnit_Framework_TestCase::assertEquals($expected, $actual);
    }

    public function testMethodChaining()
    {
        // given
        $array = ["a", "b", "c"];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();
        $predicate = function ($el) {
            return $el != "b";
        };
        $mappingFunction = function ($item) {
            return $item . "_";
        };

        // when
        $head = $enumerable
            ->select($predicate)
            ->drop(1)
            ->map($mappingFunction)
            ->head();

        // then
        PHPUnit_Framework_TestCase::assertEquals('c_', $head);
    }

    /**
     * @covers ::find
     */
    public function testFindPresent()
    {
        // given
        $array = ["a", "b", "c"];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();
        $predicate = function ($el) {
            return $el == "b";
        };

        // when
        $var = $enumerable->find($predicate);

        // then
        PHPUnit_Framework_TestCase::assertInstanceOf(Some::class, $var);
        PHPUnit_Framework_TestCase::assertEquals("b", $var->get());
    }

    /**
     * @covers ::find
     */
    public function testFindAbsent()
    {
        // given
        $array = ["a", "b", "c"];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();
        $predicate = function ($el) {
            return $el == "f";
        };

        // when
        $var = $enumerable->find($predicate);

        // then
        PHPUnit_Framework_TestCase::assertInstanceOf(None::class, $var);
    }

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
        PHPUnit_Framework_TestCase::assertEquals($expected, iterator_to_array($var));
    }

    public function flatMapProvider()
    {
        return [
            [
                [1, 2, 3, 4],
                function ($element) {
                    return [$element, -$element];
                },
                [1, -1, 2, -2, 3, -3, 4, -4]
            ],
            [
                [[1, 2], [3, 4]],
                function ($element) {
                    $element[] = 100;
                    return $element;
                },
                [1, 2, 100, 3, 4, 100]
            ],
            [
                [1, 2, [3, 5], 4],
                function ($e) {
                    return $e;
                },
                [1, 2, 3, 5, 4]
            ]
        ];
    }

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
        PHPUnit_Framework_TestCase::assertEquals($expected, $all);
    }

    public function allProvider()
    {
        return [
            [[1, 5, 4], function ($e) {
                return $e < 10;
            }, true],
            [[1, 5, 4], function ($e) {
                return $e < 5;
            }, false],
            [[], function () {
                return true;
            }, true],
            [[], function () {
                return false;
            }, true]
        ];
    }

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
        PHPUnit_Framework_TestCase::assertEquals($expected, $any);
    }

    public function anyProvider()
    {
        return [
            [[1, 5, 4], function ($e) {
                return $e > 10;
            }, false],
            [[1, 5, 4], function ($e) {
                return $e == 5;
            }, true],
            [[], function () {
                return false;
            }, false],
            [[], function () {
                return true;
            }, false]
        ];
    }

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
        PHPUnit_Framework_TestCase::assertInstanceOf(Enumerable::class, $group);
        PHPUnit_Framework_TestCase::assertEquals(array_keys($expected), array_keys(iterator_to_array($group)),
            "Group keys differ.");
        $allValuesAreEnumerables = array_reduce(array_values(iterator_to_array($group)), function ($all, $e) {
            return $all && ($e instanceof Enumerable);
        }, true);
        PHPUnit_Framework_TestCase::assertTrue($allValuesAreEnumerables);

        $actualAsArray = array_map(function ($e) {
            return iterator_to_array($e);
        }, iterator_to_array($group));
        PHPUnit_Framework_TestCase::assertEquals($expected, $actualAsArray);
    }

    public function groupByProvider()
    {
        return [
            [
                [1, 2, 10, 12],
                function ($e) {
                    return $e < 10 ? "<" : ">";
                },
                ["<" => [0 => 1, 1 => 2], ">" => [2 => 10, 3 => 12]]
            ]
        ];
    }

    /**
     * @covers ::reduce
     * @dataProvider reduceProvider
     * @param array $array
     * @param $binaryFunction
     * @param $expected
     */
    public function testReduce(array $array, $binaryFunction, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $reduced = $enumerable->reduce($binaryFunction);

        // then
        PHPUnit_Framework_TestCase::assertEquals($expected, $reduced);
    }

    public function reduceProvider()
    {
        return [
            [[10, 5, 1], function ($a, $b) {
                return $a + $b;
            }, 16],
            [["a", "b", "foo" => "c"], function ($a, $b) {
                return $a . $b;
            }, "abc"]
        ];
    }

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
        PHPUnit_Framework_TestCase::fail();
    }

    /**
     * @covers ::reduce
     * @dataProvider reduceInjectProvider
     * @param array $array
     * @param $initialValue
     * @param $binaryFunction
     * @param $expected
     */
    public function testReduceInject(array $array, $initialValue, $binaryFunction, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $reduced = $enumerable->reduce($binaryFunction, $initialValue);

        // then
        PHPUnit_Framework_TestCase::assertEquals($expected, $reduced);
    }

    public function reduceInjectProvider()
    {
        return [
            [[10, 5, 1], 2, function ($a, $b) {
                return $a + $b;
            }, 18],
            [["a", "b", "foo" => "c"], "_", function ($a, $b) {
                return $a . $b;
            }, "_abc"],
            [[], "_", function ($a, $b) {
                return $a . $b;
            }, "_"],
            [[], null, function ($a, $b) {
                return $a . $b;
            }, null]
        ];
    }

    /**
     * @covers ::reduceOption
     * @dataProvider reduceOptionProvider
     * @param array $array
     * @param $binaryFunction
     * @param $expected
     */
    public function testReduceOption(array $array, $binaryFunction, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $reduced = $enumerable->reduceOption($binaryFunction);

        // then
        PHPUnit_Framework_TestCase::assertEquals($expected, $reduced);
    }

    public function reduceOptionProvider()
    {
        return [
            [[10, 5, 1], function ($a, $b) {
                return $a + $b;
            }, Some::create(16)],
            [["a", "b", "foo" => "c"], function ($a, $b) {
                return $a . $b;
            }, Some::create("abc")],
            [[], function ($a, $b) {
                return $a . $b;
            }, None::create()]
        ];
    }

    /**
     * @covers ::reduceOption
     * @dataProvider reduceOptionInjectProvider
     * @param array $array
     * @param $initialValue
     * @param $binaryFunction
     * @param $expected
     */
    public function testReduceOptionInject(array $array, $initialValue, $binaryFunction, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $reduced = $enumerable->reduceOption($binaryFunction, $initialValue);

        // then
        PHPUnit_Framework_TestCase::assertEquals($expected, $reduced);
    }

    public function reduceOptionInjectProvider()
    {
        return [
            [[10, 5, 1], 2, function ($a, $b) {
                return $a + $b;
            }, Some::create(18)],
            [["a", "b", "foo" => "c"], "_", function ($a, $b) {
                return $a . $b;
            }, Some::create("_abc")],
            [[], "_", function ($a, $b) {
                return $a . $b;
            }, Some::create("_")],
            [[], null, function ($a, $b) {
                return $a . $b;
            }, Some::create(null)]
        ];
    }

    /**
     * @covers ::groupBy
     * @dataProvider joinProvider
     * @param array $array
     * @param $separator
     * @param $expected
     */
    public function testJoin($array, $separator, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $joined = $enumerable->join($separator);

        // then
        PHPUnit_Framework_TestCase::assertEquals($joined, $expected);
    }

    public function joinProvider()
    {
        return [
            [
                ["a", "b", "c"],
                null,
                "abc"
            ],
            [
                [],
                null,
                ""
            ],
            [
                ["a", "b", "c"],
                "-",
                "a-b-c"
            ],
            [
                [1, 2, 10, 12],
                ",",
                "1,2,10,12"
            ]
        ];
    }

    /**
     * @return \Traver\Enumerable\Builder
     */
    protected abstract function createBuilder();
}