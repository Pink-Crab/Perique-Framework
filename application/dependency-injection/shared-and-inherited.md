---
description: >-
  The dependencies being passed can be shared between instances, ideal for DB
  connections and File_System classes.
---

# Shared & Inherited

### Shared

If you set a rule with either **'shared' =&gt; true** or **'shared' =&gt; false**, this lets DICE know if the same \(cached\) instance should be used or a new one.

```php
//@file_location = config/depenecies.php

return array(
	wpdb::class => array(
		'shared' => true
		'constructParams' => array( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST),
	),	
);
```

> By default you can not pass WPDB without creating a new instance, or calling it from a global. This is a suggested to rule to have by default with a new project.

Now with the above example, we will always get the same \(custom\) instance of wpdb when passed as a dependency.

```php
class My_Class {
    protected $wpdb;
    public function __cosntruct(wpdb $wpdb){
        $this->wpdb = $wpdb;
    }
    public function get_obj_hash(){
        return spl_object_hash($this->wpdb);
    }
}

class My_Other_Class extends My_Class{}

$my_class = App::make(My_Class::class);
$my_other_class = App::make(My_Other_Class::class);
```

If we then look at the object hashes, we can see the instance of wpdb is the same, but our service objects are different.

```php
// Check the object hash for wpdb.
dump($my_class->get_object_hash()); 
// 0000000079e5f3b60000000042b31773
dump($my_other_class->get_object_hash()); 
// 0000000079e5f3b60000000042b31773

// Check the hash for the services.
dump(spl_object_hash($my_class)); 
// 000000003cc56d0d0000000007fa48c5
dump(spl_object_hash($my_other_class)); 
// 000000003re44d0d0000000005db48r4
```

### Inherit

By standard, all child classes share the same rules that have been defined for the parent class. So like our example above, we could have declared the wpdb against **My\_Service** and it would have been passed to **My\_Other\_Service**

```php
.....
My_Service::class => array(
		'constructParams' => array(
				new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST ),
		),
),	
.....
```

But if we use the 'inherit' property, we can disable this feature, leaving any child classes to be declared independently or using a defined fallback.

```php
.....
My_Service::class => array(
		'constructParams' => array(
				new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST ),
		),
		'inherit' => false
),	
.....
```

If we now tried to create an instance of the **My\_Other\_Service**, we would be passed an instance of wpdb which has not been constructed \(by default wpdb needs user, pass, table and host passing\)

Using inherit, we can create some complex setups

```php
.....
wpdb => array(
		'constructParams' => array(
				DB_USER, DB_PASSWORD, DB_NAME, DB_HOST,
		),
		'shared' => true,
),
My_Service::class => array(
		'constructParams' => array(
				new wpdb( CUSTOM_DB_USER, CUSTOM_DB_PASSWORD, CUSTOM_DB_NAME, CUSTOM_DB_HOST ),
		),
		'inherit' => false,
		'shared' => true,
),	
.....
```

With the above rules, **My\_Service** would have a connection to a custom database via wpdb, whereas every other class which extends from **My\_Service** will be given then fallback version of wpdb \(and to any other class which extends from it\)

