---
description: Used to define the schema of a MySql table.
---

# Table Schema

### create\(\)

> @param string $table\_name   The tables name.  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

While you can create an instance using `new Table_Schema();` the easiest way to create is using the `Table_Schema::create(string $table_name)` static method.

```php
$schema = Table_Schema::create('my_table');

// Create using regular constructor.
$schema = new Table_Schema();
```

### table\(\)

> @param string $table\_name   The tables name.  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

 _Used to set the table name, if the Schema is created using the `create()` static method, this isnt needed._

```php
$schema = new Table_Schema();
$schema->table('my_table');
```

### primary\(\)

> @param string $key   The key for the primary column.   
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

 Must be a defined column within the rest of the Schema. Can be defined before or after column definitions.

```php
$schema = Table_Schema::create('my_table')
    ... rest of setup ...
    ->primary('id');
```

### index\(\)

> @param \PinkCrab\Modules\Table\_Builder\Table\_Index $index Additional index.  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

Allows the setting of additional indexes for columns, see the Table\_Index docs for more details.

```php
use PinkCrab\Modules\Table_Builder\Table_Index;

$schema = Table_Schema::create('my_table')
    ... rest of setup ...
    ->index(
        Table_Index::name('my_index')->column('email')->unique()
    );
```

### column\(\)

> @param string $key Column key/name.  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

Creates a new column in the schema. This is them populated with the following column detail methods.

```php
use PinkCrab\Modules\Table_Builder\Table_Index;

$schema = Table_Schema::create('my_table')
    ... rest of setup ...
    ->column('id');
```

> The following methods should be called after creating a new column. Whenever these are used, they will amend the column details of the last one passed.  
>   
> If no column has been created, an Exception will be thrown if attempting to set properties when no columns defined.

### type\(\)

> @param string $type Columntype.  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

All valid MySql types can be passed. The type is set to uppercase automatically, so the case doesnt matter.

> As of version 0.2.0 a selection of type helper methods have been added \(see below\)

```php
use PinkCrab\Modules\Table_Builder\Table_Index;

$schema = Table_Schema::create('my_table')
    ... rest of setup ...
    ->column('id')
        ->type('int'); 
```

### nullable\(\)

> @param bool $type Is column nullable \(false by default\).  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

Sets if the column is nullable, is set to false by default.

Replaces null\(\) in version 0.2.0, null\(\) can still be called but will trigger a `DEPRECATED` notice

```php
use PinkCrab\Modules\Table_Builder\Table_Index;

$schema = Table_Schema::create('my_table')
    ... rest of setup ...
    ->column('twitter')->type('TEXT')->nullable();
```

### length\(\)

> @param int $type Defines the column length if required.  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

Sets if the column is nullable, is set to false by default.

```php
use PinkCrab\Modules\Table_Builder\Table_Index;

$schema = Table_Schema::create('my_table')
    ... rest of setup ...
    ->column('id')
        ->type('INT')
        ->length(11);
```

### default\(\)

> @param string\|null $default Sets the columns defualt.  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

Sets if the columns default value. Passing null will be parsed as NULL \(passing 'NULL' as a string will result in the string being used\). 

```php
use PinkCrab\Modules\Table_Builder\Table_Index;

$schema = Table_Schema::create('my_table')
    ... rest of setup ...
    ->column('name')
        ->type('TEXT')
        ->default('No Name');
    ->column('email')
        ->type('TEXT')
        ->null()
        ->default(NULL);
        
// The following values can not be used for defaults as they are automatically 
// converted to (MySql)function/constants.
CURRENT_TIMESTAMP
```

### auto\_increment\(\)

> @param bool $auto\_increment Auto increment column \(false by default\).  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

Sets if the column is auto incrementing, ideal for id \(primary\) columns.

```php
use PinkCrab\Modules\Table_Builder\Table_Index;

$schema = Table_Schema::create('my_table')
    ... rest of setup ...
    ->column('id')
        ->type('INT')
        ->auto_increment()
    ->primary('id');
```

### unsigned\(\)

> @param bool $unsigned Is the columns value unsigned \(false by default\).  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

Sets if the column is usigned, ideal for id \(primary\) columns.

```php
use PinkCrab\Modules\Table_Builder\Table_Index;

$schema = Table_Schema::create('my_table')
    ... rest of setup ...
    ->column('id')
        ->type('INT')
        ->auto_increment()
        ->unsigned()
    ->primary('id');
```

> The basic table details can be fetched using the schema values.

### get\_table\_name\(\)

> @return string

Returns the table name.

### get\_primary\_key\(\)

> @return string**\|null**

Returns the primary key if defined.

### get\_columns\(\)

> @return array&lt;string, array&gt;

Returns all defined columns

### get\_indexes\(\)

> @return array&lt;int, \PinkCrab\Modules\Table\_Builder\Table\_Index&gt;

Returns all defined indexes.

## Type Helper Methods

> There are a selection of type shorthand helper methods too.  
>   
> @since 0.2.0

### varchar\(\)

> @param int\|null $length  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

Sets if the column to varchar with the length if passed.

```php
$schema->column('name')->type('varchar')->length(256);

// Can be expressed as 
$schema->column('name')->varchar(256)->default('no_name');
```

### int\(\)

> @param int\|null $length  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

Sets if the column to int with the length if passed.

```php
$schema->column('age')->type('int')->length(3);

// Can be expressed as 
$schema->column('age')->int(11);
```

### float\(\)

> @param int\|null $length  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

Sets if the column to float with the length if passed.

```php
$schema->column('score')->type('float')->length(11);

// Can be expressed as 
$schema->column('score')->float(11);
```

### double\(\)

> @param int\|null $length  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

Sets if the column to double with the length if passed.

```php
$schema->column('score')->type('double')->length(11);

// Can be expressed as 
$schema->column('score')->double(11);
```

### datetime\(\)

> @param string\|null $default  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

Sets if the column to datetime with a default if defined.

```php
$schema->column('created')->type('datetime')->default('CURRENT_TIMESTAMP');

// Can be expressed as 
$schema->column('created')->datetime('CURRENT_TIMESTAMP');
```

### timestamp\(\)

> @param string\|null $default  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Schema

Sets if the column to timestamp with a default if defined.

```php
$schema->column('created')->type('timestamp')->default('CURRENT_TIMESTAMP');

// Can be expressed as 
$schema->column('created')->timestamp('CURRENT_TIMESTAMP');
```

