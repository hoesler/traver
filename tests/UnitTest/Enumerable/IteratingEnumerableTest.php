<?php

namespace UnitTest\Enumerable;


use Iterator;
use PHPUnit_Framework_TestCase;
use Traver\Enumerable\Enumerable;
use Traver\Enumerable\IteratingEnumerable;

/**
 * @coversDefaultClass \Traver\Enumerable\IteratingEnumerable
 */
class IteratingEnumerableTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers ::getIterator
     */
    public function testGetIterator()
    {
        // given
        $expected = ["a", "b", "c"];
        $iterator = new \ArrayIterator($expected);
        $enumerable = new IteratingEnumerable($iterator);

        // when
        $iterator2 = $enumerable->getIterator();

        // then
        self::assertInstanceOf(Iterator::class, $iterator2);
        self::assertEquals($expected, iterator_to_array($iterator2));
    }

    /**
     * @covers ::map
     */
    public function testMap()
    {
        // given
        $array = ["a", "b", "c"];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);
        $mappingFunction = function ($item) {
            return $item . "_";
        };

        // when
        $mapped = $enumerable->map($mappingFunction);

        // then
        self::assertInstanceOf(Enumerable::class, $mapped);
        self::assertEquals(array_map($mappingFunction, $array), iterator_to_array($mapped));
    }

    /**
     * @covers ::head
     */
    public function testHead()
    {
        // given
        $array = ["a", "b", "c"];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);

        // when
        $head = $enumerable->head();

        // then
        self::assertEquals("a", $head);
    }

    /**
     * @covers ::head
     */
    public function testHeadTwice()
    {
        // given
        $array = ["a", "b", "c"];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);

        // when
        $enumerable->head();
        $head = $enumerable->head();

        // then
        self::assertEquals($array[0], $head);
    }

    /**
     * @covers ::head
     * @expectedException \Traver\Exception\NoSuchElementException
     */
    public function testHeadEmpty()
    {
        // given
        $array = [];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);

        // when
        $enumerable->head();
    }

    /**
     * @covers ::tail
     */
    public function testTail()
    {
        // given
        $array = ["a", "b", "c"];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);

        // when
        $tail = $enumerable->tail();

        // then
        self::assertInstanceOf(Enumerable::class, $tail);
        self::assertEquals(array_slice($array, 1, null, true), iterator_to_array($tail));
    }

    /**
     * @covers ::tail
     */
    public function testTailTwice()
    {
        // given
        $array = ["a", "b", "c"];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);

        // when
        $enumerable->tail();
        $tail = $enumerable->tail();

        // then
        self::assertInstanceOf(Enumerable::class, $tail);
        self::assertEquals(array_slice($array, 1, null, true), iterator_to_array($tail));
    }

    /**
     * @covers ::toArray
     */
    public function testToArray()
    {
        // given
        $array = ["a", "b", "foo" => "c"];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);

        // when
        $toArray = $enumerable->toArray();

        // then
        self::assertEquals($array, $toArray);
    }

    /**
     * @covers ::aggregate
     */
    public function testAggregateStrings()
    {
        // given
        $array = ["a", "b", "foo" => "c"];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);
        $concat = function ($a, $b) {
            return $a . $b;
        };

        // when
        $aggregate = $enumerable->aggregate($concat, "");

        // then
        self::assertEquals("abc", $aggregate);
    }

    /**
     * @covers ::aggregate
     */
    public function testAggregateInts()
    {
        // given
        $array = [10, 5, 1];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);
        $sum = function ($a, $b) {
            return $a + $b;
        };

        // when
        $aggregate = $enumerable->aggregate($sum, 0);

        // then
        self::assertEquals(16, $aggregate);
    }

    /**
     * @covers ::countWhich
     */
    public function testCount()
    {
        // given
        $array = [10, 5, 1];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);

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
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);

        // when
        $count = $enumerable->countWhich(function ($el) {
            return $el < 10;
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
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);

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
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);
        $predicate = function ($el) {
            return $el > 5;
        };

        // when
        $dropWhile = $enumerable->dropWhile($predicate);

        // then
        self::assertInstanceOf(Enumerable::class, $dropWhile);
        self::assertEquals(array_slice($array, 1, null, true), iterator_to_array($dropWhile));
    }

    /**
     * @covers ::exists
     */
    public function testExists()
    {
        // given
        $array = [10, 5, 1];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);
        $predicate = function ($el) {
            return $el > 5;
        };

        // when
        $exists = $enumerable->exists($predicate);

        // then
        self::assertTrue($exists);
    }

    /**
     * @covers ::exists
     */
    public function testExistsNot()
    {
        // given
        $array = [10, 5, 1];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);
        $predicate = function ($el) {
            return $el > 10;
        };

        // when
        $exists = $enumerable->exists($predicate);

        // then
        self::assertFalse($exists);
    }

    /**
     * @covers ::filter
     */
    public function testFilter()
    {
        // given
        $array = ["a", "b", "c"];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);
        $predicate = function ($el) {
            return $el != "b";
        };

        // when
        $selected = $enumerable->filter($predicate);

        // then
        self::assertInstanceOf(Enumerable::class, $selected);
        self::assertEquals(array_filter($array, $predicate), iterator_to_array($selected));
    }

    public function testMethodChaining()
    {
        // given
        $array = ["a", "b", "c"];
        $iterator = new \ArrayIterator($array);
        $enumerable = new IteratingEnumerable($iterator);
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
        self::assertEquals('c_', $head);
    }


}
