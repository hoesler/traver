# Traver

A PHP library which provides collection classes implementing the [pipeline pattern](http://martinfowler.com/articles/collection-pipeline).

[![Build Status](https://img.shields.io/travis/hoesler/traver/master.svg?style=flat-square)](https://travis-ci.org/PHP-DI/PHP-DI)

PHP has only limited support for object oriented collections and none implements the pipeline pattern, which is available in many modern languages like Hack, Ruby, Scala or Clojure. All existing libraries I found on the web didn't satisfy me, but served as an inspiration: [functional-php](https://github.com/lstrojny/functional-php), [fluent-traversable](https://github.com/psliwa/fluent-traversable) or [immutable.php](https://github.com/jkoudys/immutable.php).

## Status
This library is in early alpha state.

## Usage
### Callbacks of pipeline methods
As PHP conflates array and hash, all pipeline methods that accept callbacks should cover both usages. This requires that the callbacks should be called with either key and value or just the value, depending on the usage. For closures it would be okay to always pass value and key. The key argument with simply be ignored: `$callback = function ($value) {...}; $callback($value, $key);`. For PHP functions like 'is_string', however, this wouldn't work, because they require exactly one argument.

The second option is to offer two versions of the same method with with different method names as in Hack. This would of course inflate the number of methods. Probably for this reason, Hack offers both versions only for map and filter.

A third option, which I chose here, is to always pass both arguments (value, key) but use reflection to check for the number of accepted parameters. Than wrap callbacks which accept only (exactly) one argument in a closure with accepts both arguments. This comes at a negligible cost of performance but make the methods much more versatile:
`$collection->select('is_string');`.

## Examples
```php
use function Traver\in; // creates a new ImmutableMap from an array

$even = function ($number) { return $number & 1 == 0; };
in([1, 2, 3])->select($even); // [1 => 2]
in(['a', 'b', 'c'])->map('ucfirst'); // ['A', 'B', 'C']
```
