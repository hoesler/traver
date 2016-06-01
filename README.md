# Traver

A PHP library which provides mutable and immutable collections with methods common in functional programming.

[![Build Status](https://img.shields.io/travis/hoesler/traver/master.svg?style=flat-square)](https://travis-ci.org/hoesler/traver)

## About
PHP has only limited support for object oriented collections and none implements the [pipeline pattern](http://martinfowler.com/articles/collection-pipeline), which is available in many modern languages like Hack, Ruby, Scala or Clojure. All existing libraries I found on the web did not satisfy me, but served as an inspiration: [functional-php](https://github.com/lstrojny/functional-php), [fluent-traversable](https://github.com/psliwa/fluent-traversable) or [immutable.php](https://github.com/jkoudys/immutable.php).

The API design is mainly guided by Ruby and Scala but respecting that PHP is a different language with different concepts.

## Status
No version has been released yet.

## Usage
### Collections
All collections in Traver implement the *Pipeable* interface which provides a set of methods common to collections like *all/any*, *count/countWhich*, *drop/dropWhile*, *find*, *flatMap*, *groupBy, *head/tail*, *reduce*, *reject/select*, *slice*, *sort/sortBy*, *take/takeWhile*.

Traver comes with the following default collections:

- **MutableVector**: A mutable collection of enumerated elements backed by a [SplFixedArray](http://php.net/manual/en/class.splfixedarray.php).
- **ImmutableVector**: An immutable collection of enumerated elements backed by a [SplFixedArray](http://php.net/manual/en/class.splfixedarray.php).
- **MutableMap**: A mutable dictionary-like collection of unique keys and their values backed by a [ArrayObject](http://php.net/manual/en/class.arrayobject.php).
- **ImmutableMap**: An immutable dictionary-like collection of unique keys and their values backed by a [ArrayObject](http://php.net/manual/en/class.arrayobject.php).

All collections support conversion to a *view* with lazy evaluation of its elements.

### Callbacks in pipeline methods
As PHP conflates array and hash, all pipeline methods that accept callbacks should cover both usages. This requires that the callbacks can be called with either key and value or just the value, depending on the usage. For closures it would be okay to always pass value and key. The key argument with simply be ignored: `$callback = function ($value) {...}; $callback($value, $key);`. For primitive functions like 'is_string', however, this would fail, because they require *exactly* one argument.

The second option is to offer two versions of the same method with with different method names as in Hack. This would of course inflate the number of methods. Probably for this reason, Hack offers both versions only for map and filter.

A third option, which I chose here, is to always pass both arguments (value, key) but use reflection to check for the number of accepted parameters. Than wrap callbacks which accept only (exactly) one argument in a closure with accepts both arguments. This comes at a negligible cost of performance but makes the methods much more versatile:
`$collection->select('is_string');`.

## Examples
```php
use function Traver\in; // creates a new ImmutableMap from an array
use function Traver\vector;
use function Traver\hash;
use function Traver\mutable_vector;
use function Traver\mutable_hash;

$even = function ($number) { return $number & 1 == 0; };
vector([1, 2, 3])->select($even); // [1 => 2]
vector(['a', 'b', 'c'])->map('ucfirst'); // ['A', 'B', 'C']
array_map(['a', 'b', 'c'], 'ucfirst');

$vec = vector([1, 2, 3]);
$map = map(['a' => 1, 'b' => 2]);

$vec = \Traver\mutable_vector();
$map = \Traver\mutable_map();

\Traver\range(1, INF)->view()->select($even)->first(5); // [2, 4, 6, 8, 10] 
```

