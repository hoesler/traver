<?php


namespace Traver\Test\UnitTest\Collection;


use PhpOption\None;
use PhpOption\Some;
use Traver\Callback\Comparators;
use Traver\Callback\OperatorCallbacks;

abstract class AbstractVectorTest extends AbstractPipeableTest
{

    public function asTraversableProvider()
    {
        return [
            [[1, 2, 3]]
        ];
    }

    public function mapProvider()
    {
        return [
            'one callback argument' => [["a", "b", "c"], function ($value) {
                return $value . "_";
            }, ["a_", "b_", "c_"]],
            'two callback arguments' => [["a", "b", "c"], function ($value, $key) {
                return $key . $value;
            }, ["0a", "1b", "2c"]],
            'php function with exactly one argument' => [["a", "b", "c"], 'ucfirst', ["A", "B", "C"]],
            'empty array' => [[], function ($value) {
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
        $array = [3, 2, 1];
        $builder = $this->createBuilder();
        $builder->addAll($array);
        $enumerable = $builder->build();

        // when
        $head = $enumerable->head();

        // then
        self::assertEquals(3, $head);
    }


    public function tailProvider()
    {
        return [
            [[3, 2, 1], [2, 1]]
        ];
    }

    public function emptyProvider()
    {
        return [
            [[], true],
            [["a"], false]
        ];
    }


    public function sliceProvider()
    {
        return [
            [["a", "b", "c"], 0, 0, []],
            [["a", "b", "c"], 0, 3, ["a", "b", "c"]],
            [["a", "b", "c"], 0, 1, ["a"]],
            [["a", "b", "c"], 1, 2, ["b"]],
            [["a", "b", "c"], 2, 1, []],
            [["a", "b", "c"], 2, 23, ["c"]],
            [[], 0, 0, []]
        ];
    }


    public function eachProvider()
    {
        return [
            [[1, 2, 3]]
        ];
    }

    public function toArrayProvider()
    {
        return [
            [["a", "b", "c"], ["a", "b", "c"]],
            [["a", "b", "c"], ["a", "b", "c"], true],
            [["a", "b", "c"], ["a", "b", "c"], false]
        ];
    }


    public function countProvider()
    {
        return [
            [[10, 5, 1], 3]
        ];
    }


    public function countWhichProvider()
    {
        return [
            [[10, 5, 1], 2, function ($value) {
                return $value < 10;
            }]
        ];
    }


    public function dropProvider()
    {
        return [
            [[10, 5, 1], [1], 2]
        ];
    }


    public function dropWhileProvider()
    {
        return [
            [[10, 5, 1], [5, 1], function ($value) {
                return $value > 5;
            }]
        ];
    }


    public function takeProvider()
    {
        return [
            [[10, 5, 1], 2, [10, 5]],
            [[10, 5, 1], 0, []],
            [[], 2, []]
        ];
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


    public function selectProvider()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return [
            [["a", "b", "c"], function ($value) {
                return $value != "b";
            }, ["a", "c"]],
            [["a", "b", "c"], function ($value, $key) {
                return $key != 1;
            }, ["a", "c"]],
            [[24, "b", null], 'is_string', ["b"]]
        ];
    }


    public function rejectProvider()
    {
        return [
            [[3, 2, 1], [3, 1], function ($el) {
                return $el == 2;
            }]
        ];
    }


    public function findProvider()
    {
        return [
            [[3, 2, 1], Some::create(2), function ($el) {
                return $el == 2;
            }],
            [[3, 2, 1], None::create(), function ($el) {
                return $el == 4;
            }]
        ];
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


    public function groupByProvider()
    {
        return [
            [
                [1, 2, 10, 12],
                function ($e) {
                    return $e < 10 ? 3 : 4;
                },
                [3 => [1, 2], 4 => [10, 12]]
            ]
        ];
    }


    public function reduceProvider()
    {
        return [
            'sum with closure' => [[10, 5, 1], 16, function ($a, $b) {
                return $a + $b;
            }],
            'sum with static method' => [[1, 2, 3], 6, array(OperatorCallbacks::class, 'add')],
            [[10, 5, 1], 18, function ($a, $b) {
                return $a + $b;
            }, 2],
            'concat with initial' => [["a", "b", "c"], "_abc", function ($a, $b) {
                return $a . $b;
            }, "_"],
            'empty with initial' => [[], "_", function ($a, $b) {
                return $a . $b;
            }, "_"]
        ];
    }


    public function reduceOptionProvider()
    {
        return [
            'sum closure without initial' => [[10, 5, 1], Some::create(16), function ($a, $b) {
                return $a + $b;
            }],
            'concat closure without initial' => [["a", "b", "c"], Some::create("abc"), function ($a, $b) {
                return $a . $b;
            }],
            'empty without initial' => [[], None::create(), function ($a, $b) {
                return $a . $b;
            }],
            'sum closure with initial' => [[10, 5, 1], Some::create(18), function ($a, $b) {
                return $a + $b;
            }, 2],
            'concat closure with initial' => [["a", "b", "c"], Some::create("_abc"), function ($a, $b) {
                return $a . $b;
            }, "_"],
            'empty with initial' => [[], Some::create("_"), function ($a, $b) {
                return $a . $b;
            }, "_"]
        ];
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


    public function keysProvider()
    {
        return [
            [["a", "b", "c"], [0, 1, 2]],
            [[], []]
        ];
    }


    public function valuesProvider()
    {
        return [
            [["a", "b", "c"], ["a", "b", "c"]],
            [[], []]
        ];
    }


    public function entriesProvider()
    {
        return [
            [["a", "b", "c"], [[0, "a"], [1, "b"], [2, "c"]]],
            [[], []]
        ];
    }


    public function sortProvider()
    {
        return [
            [
                [1, 2, 4, 3],
                [1, 2, 3, 4]
            ],
            [
                [1, 2, 4, 3],
                [1, 2, 3, 4],
                Comparators::naturalComparator()
            ]
        ];
    }


    public function sortByProvider()
    {
        /** @noinspection PhpUnusedParameterInspection */
        return [
            [
                ["a", "c", "b", "d"],
                function ($value) {
                    return $value;
                },
                ["a", "b", "c", "d"]
            ],
            [
                ["a", "c", "b", "d"],
                function ($value, $key) {
                    return $key;
                },
                ["a", "c", "b", "d"]
            ],
            [
                ["aaa", "aa", "aaaa", "a"],
                'strlen',
                ["a", "aa", "aaa", "aaaa"]
            ]
        ];
    }
}