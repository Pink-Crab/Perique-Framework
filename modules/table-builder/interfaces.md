---
description: >-
  The Table Builder comes with 2 primary interfaces, which allow for polymorphic
  use.
---

# Interfaces

### SQL\_Schema

This defines the table's schema.

```php
interface SQL_Schema {
	/**
	 * Constructs the table.
	 *
	 * @param \PinkCrab\Modules\Table_Builder\Interfaces\SQL_Builder $builder
	 * @return void
	 */
	public function create_table( SQL_Builder $builder ): void;

	/**
	 * Gets the defined table name
	 *
	 * @return string
	 */
	public function get_table_name(): string;

	/**
	 * Gets the defined primary key
	 *
	 * @return string
	 */
	public function get_primary_key(): string;

	/**
	 * Returns all the defined indexes.
	 *
	 * @return array<int, \PinkCrab\Modules\Table_Builder\Table_Index>
	 */
	public function get_indexes(): array;

	/**
	 * Returns all the defined columns
	 *
	 * @return array<int, array>
	 */
	public function get_columns(): array;
}
```

### SQL\_Builder

Defines the builder for the construction of the table.

```php
interface SQL_Builder {

	/**
	 * Builds the passed schema.
	 *
	 * @param \PinkCrab\Modules\Table_Builder\Interfaces\SQL_Schema $schema
	 * @return void
	 */
	public function build( SQL_Schema $schema ): void;
}
```

