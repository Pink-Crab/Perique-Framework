---
description: The PinkCrab View interface.
---

# Renderable \(Interface\)

### Renderable

Defines the only function which much be implemented to be used.

```php
namespace PinkCrab\Core\Interfaces;
interface Renderable {
		/**
	 * Display a view and its context.
	 *
	 * @param string $view
	 * @param iterable<string, mixed> $data
	 * @param bool $print
	 * @return void|string
	 */
	public function render( string $view, iterable $data, bool $print = true );
}
```

