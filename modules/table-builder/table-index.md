---
description: >-
  Table_Index allows for the defining of Indexes, including Unique, Foreign Key,
  Text Search and Hash.
---

# Table Index

### name\(\)

> @param string $keyname  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Index

Usually a Table\_Index is constructed as the input for Table\_Schema::index\(\). So while you can construct using new Table\_Index\($name\), it's best to use the name\(\) consturctor.

```php
$schema->index(
    Table_Index::name('my_index')
);

// But can be done as.
$my_index = new Table_Index('my_index');
$schema->index($my_index)
```

### column\(\)

> @param string $column  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Index  
> @required

This denotes the column to which the index belongs to, the column must be defined within the schema.

```php
// Ensure all email address are unique.

$schema->column('email')->type('varchar')->length(128);
$schema->index( Table_Index::name('my_index')->column('email')->unique() );
```

### unique\(\)

> @param bool $unique  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Index

Sets the index of the column to be unique.

```php
// Ensure all email address are unique.

$schema->column('email')->type('varchar')->length(128);
$schema->index( Table_Index::name('my_index')->column('email')->unique() );
```

### full\_text\(\)

> @param string $full\_text  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Index

Sets the index to make use of the Full-Text natural search functionality.

```php
$schema->column('contents')->type('MEDIUMTEXT');
$schema->index(
    Table_Index::name('contents')->column('contents')->full_text()
);
```

### hash\(\)

> @param string $hash  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Index

Sets the index type to hash

```php
$schema->column('email')->type('varchar')->length(128);
$schema->index(
    Table_Index::name('my_index')->column('email')->unique()->hash()
);
```

## Cross Table References.

### foreign\_key\(\)

> @param string $foreign\_key  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Index

Sets if the index is to be used as a foreign key.

```php
$schema->column('email')->type('varchar')->length(128);
$schema->index(Table_Index::name('my_index')->column('email')->foreign_key());
```

### reference\_table\(\)

> @param string $reference\_table  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Index

Sets the table to use as the reference for the reference link.

```php
$schema->column('email')->type('varchar')->length(128);
$schema->index(
Table_Index::name('my_index')
    ->column('email')
    ->reference_table('some_other_table')
);
```

### reference\_column\(\)

> @param string $reference\_column  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Index

Sets the column to from the table defined in reference\_table\(\)

```php
$schema->column('email')->type('varchar')->length(128);
$schema->index(
Table_Index::name('my_index')
    ->column('email')
    ->reference_table('some_other_table')
    ->reference_column('user_ref')
);
```

### on\_update\(\)

> @param string $action  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Index

Set the operation to be carried out if the parent row is updated.   
_Accepts all valid MYSQL actions._

```php
$schema->column('email')->type('varchar')->length(128);
$schema->index(
Table_Index::name('my_index')
    ->column('email')
    ->reference_table('some_other_table')
    ->reference_column('user_ref')
    ->on_update('CASCADE') 
    // Now the if the parent user_ref is udpated, the children will be.
);
```

### on\_delete\(\)

> @param string $action  
> @return \PinkCrab\Modules\Table\_Builder\Table\_Index

Set the operation to be carried out if the parent row is deleted.   
_Accepts all valid MYSQL actions._

```php
$schema->column('email')->type('varchar')->length(128);
$schema->index(
Table_Index::name('my_index')
    ->column('email')
    ->reference_table('some_other_table')
    ->reference_column('user_ref')
    ->on_delete('CASCADE') 
    // Now the if the parent is deleted, all child users which
    // link to it will be removed.
);
```

