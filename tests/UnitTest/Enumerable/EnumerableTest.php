<?php


namespace Traver\Test\UnitTest\Enumerable;


use PhpOption\None;
use PhpOption\Some;
use PHPUnit_Framework_TestCase;
use Traver\Callback\Comparators;
use Traver\Callback\OperatorCallbacks;
use Traver\Enumerable\Enumerable;
use Traversable;

/**
 * Class EnumerableTest
 * @package Traver\Test\UnitTest\Enumerable
 * @coversDefaultClass \Traver\Enumerable\Enumerable
 */
abstract class EnumerableTest extends PHPUnit_Framework_TestCase
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
        self::assertInstanceOf(Traversable::class, $iterable2);
        self::assertEquals($expected, iterator_to_array($iterable2));
    }

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

    public function mapProvider()
    {
        return [
            [["a", "b", "c"], function ($value) {
                return $value . "_";
            }, ["a_", "b_", "c_"]],
            [["a", "b", "c"], function ($value, $key) {
                return $key . $value;
            }, ["0a", "1b", "2c"]],
            [["a", "b", "c"], 'ucfirst', ["A", "B", "C"]],
            [[], function ($value) {
                return $value;
            }, []]
        ];
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
        self::assertEquals("a", $head);
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
        self::fail();
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
        self::assertInstanceOf(Enumerable::class, $tail);
        self::assertEquals(array_slice($array, 1, null, true), iterator_to_array($tail));
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
        self::fail("tail did not throw an UnsupportedOperationException on empty collection");
    }

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

    public function emptyProvider()
    {
        return [
            [[], true],
            [["a"], false]
        ];
    }

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

    public function sliceProvider()
    {
        return [
            [["a", "b", "c"], 0, 0, []],
            [["a", "b", "c"], 0, 3, ["a", "b", "c"]],
            [["a", "b", "c"], 0, 1, ["a"]],
            [["a", "b", "c"], 1, 2, [1 => "b"]],
            [["a", "b", "c"], 2, 1, []],
            [["a", "b", "c"], 2, 23, [2 => "c"]],
            [[], 0, 0, []]
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
            $newArray[$key] = $value . $key;
        });

        // then
        self::assertEquals(['a0', 'b1', 'c2'], $newArray);
    }

    /**
     * @covers ::each
     */
    public function testEachOneParameters()
    {
        // given
        $array = ["a", "b", "c"];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();
        $newArray = [];

        // when
        $enumerable->each(function ($value) use (&$newArray) {
            $newArray[] = $value;
        });

        // then
        self::assertEquals($array, $newArray);
    }

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

    public function toArrayProvider()
    {
        return [
            [["a", "b", "foo" => "c"], ["a", "b", "foo" => "c"]],
            [["a", "b", "foo" => "c"], ["a", "b", "foo" => "c"], true],
            [["a", "b", "foo" => "c"], ["a", "b", "c"], false]
        ];
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
        self::assertEquals(3, $count);
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
        $count = $enumerable->countWhich(function ($value) {
            return $value < 10;
        });

        // then
        self::assertEquals(2, $count);
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
        self::assertInstanceOf(Enumerable::class, $drop);
        self::assertEquals(array_slice($array, 2, null, true), iterator_to_array($drop));
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
        $predicate = function ($value) {
            return $value > 5;
        };

        // when
        $dropWhile = $enumerable->dropWhile($predicate);

        // then
        self::assertInstanceOf(Enumerable::class, $dropWhile);
        self::assertEquals(array_slice($array, 1, null, true), iterator_to_array($dropWhile));
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
        self::assertInstanceOf(Enumerable::class, $taken);
        self::assertEquals($expected, iterator_to_array($taken));
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

    public function takeWhileProvider()
    {
        return [
            [[10, 5, 1], function ($value) {
                return $value >= 5;
            }, [10, 5]],
            [[10, 5, 1], function ($value) {
                return $value <= 5;
            }, []],
            [[], function () {
                return true;
            }, []]
        ];
    }

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

    public function selectProvider()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return [
            [["a", "b", "c"], function ($value) {
                return $value != "b";
            }, [0 => "a", 2 => "c"]],
            [["a", "b", "c"], function ($value, $key) {
                return $key != 1;
            }, [0 => "a", 2 => "c"]],
            [[24, "b", null], 'is_string', [1 => "b"]]
        ];
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
        self::assertInstanceOf(Enumerable::class, $filtered);
        $actual = iterator_to_array($filtered);
        $expected = array_filter($array, function ($value, $key) use ($predicate) {
            return !$predicate($value, $key);
        }, ARRAY_FILTER_USE_BOTH);
        self::assertEquals($expected, $actual);
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
        self::assertInstanceOf(Some::class, $var);
        self::assertEquals("b", $var->get());
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
        self::assertInstanceOf(None::class, $var);
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
        self::assertEquals($expected, iterator_to_array($var));
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
        self::assertEquals($expected, $all);
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
        self::assertEquals($expected, $any);
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

    public function reduceProvider()
    {
        return [
            [[10, 5, 1], 16, function ($a, $b) {
                return $a + $b;
            }],
            [["a", "b", "foo" => "c"], "abc", function ($a, $b) {
                return $a . $b;
            }],
            [[1, 2, 3], 6, array(OperatorCallbacks::class, 'add')],
            [[10, 5, 1], 18, function ($a, $b) {
                return $a + $b;
            }, 2],
            [["a", "b", "foo" => "c"], "_abc", function ($a, $b) {
                return $a . $b;
            }, "_"],
            [[], "_", function ($a, $b) {
                return $a . $b;
            }, "_"],
            [[], null, function ($a, $b) {
                return $a . $b;
            }, null]
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

    public function reduceOptionProvider()
    {
        return [
            [[10, 5, 1], Some::create(16), function ($a, $b) {
                return $a + $b;
            }],
            [["a", "b", "foo" => "c"], Some::create("abc"), function ($a, $b) {
                return $a . $b;
            }],
            [[], None::create(), function ($a, $b) {
                return $a . $b;
            }],
            [[10, 5, 1], Some::create(18), function ($a, $b) {
                return $a + $b;
            }, 2],
            [["a", "b", "foo" => "c"], Some::create("_abc"), function ($a, $b) {
                return $a . $b;
            }, "_"],
            [[], Some::create("_"), function ($a, $b) {
                return $a . $b;
            }, "_"],
            [[], Some::create(null), function ($a, $b) {
                return $a . $b;
            }, null]
        ];
    }

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

    public function joinProvider()
    {
        return [
            [
                ["a", "b", "c"],
                "abc"
            ],
            [
                [],
                ''
            ],
            [
                [],
                '',
                '-'
            ],
            [
                ["a", "b", "c"],
                "a-b-c",
                "-"
            ],
            [
                [1, 2, 10, 12],
                "1,2,10,12",
                ","
            ]
        ];
    }

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

    public function keysProvider()
    {
        return [
            [["a", "b", "c"], [0, 1, 2]],
            [["a" => 1, "b" => 2, "c" => 3], ["a", "b", "c"]],
            [[], []]
        ];
    }

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

    public function valuesProvider()
    {
        return [
            [[3 => "a", 4 => "b", 30 => "c"], ["a", "b", "c"]],
            [["a" => 1, "b" => 2, "c" => 3], [1, 2, 3]],
            [[], []]
        ];
    }

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

    public function entriesProvider()
    {
        return [
            [["a", "b", "c"], [[0, "a"], [1, "b"], [2, "c"]]],
            [["a" => 1, "b" => 2, "c" => 3], [["a", 1], ["b", 2], ["c", 3]]],
            [[], []]
        ];
    }

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

    public function sortProvider()
    {
        return [
            [
                ["a" => 1, "c" => 2, "b" => 4, "d" => 3],
                ["a" => 1, "b" => 4, "c" => 2, "d" => 3]
            ],
            [
                ["a" => 1, "c" => 2, "b" => 4, "d" => 3],
                ["a" => 1, "b" => 4, "c" => 2, "d" => 3],
                Comparators::naturalComparator()
            ],
            [
                ["a" => 1, "c" => 2, "b" => 4, "d" => 3],
                ["a" => 1, "c" => 2, "b" => 4, "d" => 3],
                function () {
                    return 0;
                }
            ]
        ];
    }

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

    public function sortByProvider()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return [
            [
                ["a" => 1, "c" => 2, "b" => 4, "d" => 3],
                function ($value) {
                    return $value;
                },
                ["a" => 1, "c" => 2, "d" => 3, "b" => 4]
            ],
            [
                ["a" => 1, "c" => 2, "b" => 4, "d" => 3],
                function ($value, $key) {
                    return $key;
                },
                ["a" => 1, "b" => 4, "c" => 2, "d" => 3]
            ],
            [
                ["aaa", "aa", "aaaa", "a"],
                'strlen',
                [3 => "a", 1 => "aa", 0 => "aaa", 2 => "aaaa"]
            ]
        ];
    }

    /**
     * @return \Traver\Enumerable\Builder
     */
    protected abstract function createBuilder();
}