# Traver
## Callbacks
As PHP conflates array and hash, all pipeline methods that accept callbacks should cover both usages. This requires that the callbacks should be called with either key and value or just the value, depending on the usage. For closures it would be okay to always pass value and key. The key argument with simply be ignored: ```
$callback = function ($value) {...};
$callback($value, $key);
```.
For PHP functions like 'is_string', however, this wouldn't work, because they require exactly one argument.

The second option is to offer two versions of the same method with with different method names as in Hack. This would of course inflate the number of methods. Probably for this reason, Hack offers both versions only for map and filter.
A third option, which I chose here, is to always pass both arguments (value, key) but use reflection to check for the number of accepted parameters. Than wrap callbacks, which accept only (exactly) one argument in a closure with accepts both arguments. This comes at a negligible cost of performance but make the methods much more versatile.

## Examples
```
in([1, 2, 3])->reduce(op("+"));
in(['a', 'b', 'c'])->map('ucfirst'); // ['A', 'B', 'C']
```