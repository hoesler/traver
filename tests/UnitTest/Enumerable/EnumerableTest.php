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
        $filtered = $enumerable->filter($predicate);

        // then
        PHPUnit_Framework_TestCase::assertInstanceOf(Enumerable::class, $filtered);
        PHPUnit_Framework_TestCase::assertEquals(array_filter($array, $predicate), iterator_to_array($filtered));
    }

    /**
     * @covers ::filterNot
     */
    public function testFilterNot()
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
        $filtered = $enumerable->filterNot($predicate);

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
            ->filter($predicate)
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
     * @covers ::foldLeft
     * @dataProvider foldLeftProvider
     * @param array $array
     * @param $initialValue
     * @param $binaryFunction
     * @param $expected
     */
    public function testFoldLeft(array $array, $initialValue, $binaryFunction, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $folded = $enumerable->foldLeft($initialValue, $binaryFunction);

        // then
        PHPUnit_Framework_TestCase::assertEquals($expected, $folded);
    }

    public function foldLeftProvider()
    {
        return [
            [[10, 5, 1], 0, function ($a, $b) {
                return $a + $b;
            }, 16],
            [["a", "b", "foo" => "c"], "", function ($a, $b) {
                return $a . $b;
            }, "abc"]
        ];
    }

    /**
     * @covers ::foldLeft
     * @dataProvider foldLeftProvider
     * @param array $array
     * @param $initialValue
     * @param $binaryFunction
     * @param $expected
     */
    public function testFoldRight(array $array, $initialValue, $binaryFunction, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $folded = $enumerable->foldLeft($initialValue, $binaryFunction);

        // then
        PHPUnit_Framework_TestCase::assertEquals($expected, $folded);
    }

    public function foldRightProvider()
    {
        return [
            [[10, 5, 1], 0, function ($a, $b) {
                return $a - $b;
            }, -14],
            [["a", "b", "foo" => "c"], "", function ($a, $b) {
                return $a . $b;
            }, "cba"]
        ];
    }

    /**
     * @covers ::foldLeft
     * @dataProvider forallProvider
     * @param array $array
     * @param $predicate
     * @param $expected
     */
    public function testForall(array $array, $predicate, $expected)
    {
        // given
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $all = $enumerable->forall($predicate);

        // then
        PHPUnit_Framework_TestCase::assertEquals($expected, $all);
    }

    public function forallProvider()
    {
        return [
            [[1, 5, 4], function ($e) {
                return $e < 10;
            }, true],
            [[1, 5, 4], function ($e) {
                return $e < 5;
            }, false],
            [[], function () {
                return false;
            }, true]
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
     * @return \Traver\Enumerable\Builder
     */
    protected abstract function createBuilder();
}