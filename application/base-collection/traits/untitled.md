---
description: >-
  The indexed trait adds in methods to make working with collections that have
  key values possible. Can be added to any extended trait, but is not included
  in the base Collection object.
---

# Indexed

### Custom Collection

To create a custom collection that uses the Sequence trait, just do the following. You can overwrite any of the existing methods, or use other traits if needed.

```php
use PinkCrab\Core\Collection\Collection;
use PinkCrab\Core\Collection\Traits\Indexed;

/**
 * Custom class which uses the Indexed trait
 */
class Indexed_Collection extends Collection {
	use Indexed;
}
```

### Indexed::has\(\)

> @param int\|float\|string $key   
> @return bool

Reverses the data internally to the collection, returns the same instance.

```php
$collection = new Indexed_Collection([
    'a' => 1,
    'b' => 2,
]);
dump($collection->has('b')); // true
dump($collection->has('d')); // false
```

### Indexed::get\(\)

> @param int\|float\|string $key   
> @return mixed  
> @thows OutOfRangeException if key doesnt exist.

Reverses the data internally to the collection, returns the same instance.

```php
$collection = new Indexed_Collection([
    'a' => 1,
    'b' => 2,
]);
dump($collection->get('b')); // 1
dump($collection->get('d')); // Throws OutOfRangeException
```

### Indexed::set\(\)

> @param int\|float\|string $key   
> @param mixed $value  
> @return Existing Collection

Reverses the data internally to the collection, returns the same instance.

```php
$collection = new Indexed_Collection([
    'a' => 1,
    'b' => 2,
]);
$collection->set( 'b', 'CUSTOM' );
dump($collection->get('b')); // CUSTOM
```

### Indexed::find\(\)

@param mixed $value   
@return int\|float\|string\|false

Returns the first index which matches that passed to find. Works with arrays and objects, although with objects it finds matches on the actual instance, no values \(see example\).

```php
$collection = new Indexed_Collection([
    'a' => 1,
    'b' => 2,
    'c' => 1,
]);
dump($collection->find(1)); // 'a'
dump($collection->find(3)); // false

// With objects. 

$obj_a = new class(){
	public $property = 'value';
};
$obj_b = (object) array( 'property' => 'value' );

$obj_collection = new Indexed_Collection([
    'a' => $obj_a,
    'b' => $obj_b,
    'c' => $obj_a,
]);
dump($collection->find($obj_a)); // 'a'
dump($collection->find(new class(){	public $property = 'value';})); // false
```

### Indexed::remove\(\)

@param int\|float\|string $index   
@return mixed  
@throws OutOfRangeException if key doesnt exist.

Returns the first index which matches that passed to find. Works with arrays and objects, although with objects it finds matches on the actual instance, no values \(see example\).

```php
$collection = new Indexed_Collection([
    'a' => 1,
    'b' => 2,
    'c' => 1,
]);
dump($collection->remove('a')); // 1
dump($collection); // ['b' => 2, 'c' => 1]

// Cant remove unset keys.
dump($collection->remove('d')); // Throws OutOfRangeException
```

