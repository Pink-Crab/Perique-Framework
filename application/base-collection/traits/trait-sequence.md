---
description: >-
  The Sequence can be added to any extended collection, this give access to
  functionality more suited for none indexed arrays/lists.
---

# Sequence

### Custom Collection

To create a custom collection that uses the Sequence trait, just do the following. You can overwrite any of the existing methods, or use other traits if needed.

```php
use PinkCrab\Core\Collection\Collection;
use PinkCrab\Core\Collection\Traits\Sequence;

/**
 * Custom class which uses the Sequence trait
 */
class Sequence_Collection extends Collection {
	use Sequence;
}
```

### Sequence::reverse\(\)

> @return Existing Collection

Reverses the data internally to the collection, returns the same instance.

```php
$collection = new Sequence_Collection([5,4,3,2,1]);
$collection->reverse();
dump($collection); // 1,2,3,4,5
```

### Sequence::reversed\(\)

> @return New Collection

Creates a new collection, with the contents reversed.

```php
$collection = new Sequence_Collection([5,4,3,2,1]);
$reversed = $collection->reversed();
dump($reversed); // 1,2,3,4,5
dump($collection); // 5,4,3,2,1
```

### Sequence::rotate\(\)

The sequence can be rotated either clockwise with a positive step value or counterclockwise with a negative.

```php
$collection = new Sequence_Collection([1,2,3,4,5]);
$collection->rotate(3);
dump($collection); // 4,5,1,2,3
$collection->rotate(-1);
dump($collection); // 3,4,5,1,2
```

### Sequence::first\(\)

Gets the first value form the collection, without removing it \(like shift\(\)\)

> @return mixed  
> @throws UnderflowException if the collection is empty.

```php
$collection = new Sequence_Collection([1,2,3,4,5]);
dump($collection->first()); // 1
```

### Sequence::last\(\)

Gets the last value form the collection, without removing it \(like pop\(\)\)

> @return mixed  
> @throws UnderflowException if the collection is empty.

```php
$collection = new Sequence_Collection([1,2,3,4,5]);
dump($collection->last()); // 5
```

### Sequence::sum\(\)

Adds up all of the numerical values within the collection.

> @return int\|float

```php
$collection = new Sequence_Collection([1, 'tree', '2', 5, 9.9]);
dump($collection->sum()); // 17.9 (1+2+5+9.9)
```

