<?php


namespace Traver\Test\UnitTest\Enumerable;


use Iterator;
use PhpOption\None;
use PhpOption\Some;
use PHPUnit_Framework_TestCase;
use Traver\Enumerable\Enumerable;

/**
 * Class EnumerableTest
 * @package Traver\Test\UnitTest\Enumerable
 * @coversDefaultClass \Traver\Enumerable\Enumerable
 */
trait EnumerableTest
{
    /**
     * @covers ::getIterator
     */
    public function testGetIterator()
    {
        // given
        $expected = ["a", "b", "c"];
        $builder = $this->createBuilder();
        $builder->addAll($expected);
        $enumerable = $builder->build();

        // when
        $iterable2 = $enumerable->getIterator();

        // then
        PHPUnit_Framework_TestCase::assertInstanceOf(Iterator::class, $iterable2);
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
     * @covers ::aggregate
     */
    public function testAggregateStrings()
    {
        // given
        $array = ["a", "b", "foo" => "c"];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();
        $concat = function ($a, $b) {
            return $a . $b;
        };

        // when
        $aggregate = $enumerable->aggregate($concat, "");

        // then
        PHPUnit_Framework_TestCase::assertEquals("abc", $aggregate);
    }

    /**
     * @covers ::aggregate
     */
    public function testAggregateInts()
    {
        // given
        $array = [10, 5, 1];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();
        $sum = function ($a, $b) {
            return $a + $b;
        };

        // when
        $aggregate = $enumerable->aggregate($sum, 0);

        // then
        PHPUnit_Framework_TestCase::assertEquals(16, $aggregate);
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
     * @return \Traver\Enumerable\Builder
     */
    protected abstract function createBuilder();
}