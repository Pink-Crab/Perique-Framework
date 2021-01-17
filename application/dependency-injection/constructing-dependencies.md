---
description: >-
  Like using interface and abstract class, DICE gives up the ability to
  construct the objects we are injecting.
---

# Constructing Dependencies

```php
interface Cache{
    public function get();
}

class File_Cache implements Cache { 
    protected $location, $extension;
    
    public function __construct(string $location, string $extension){
        $this->location = $location;
        $this->extension = $extension;
    }
    
    public function maybe_create_dir(){
        // Creates the dir for cache.
    }
    
    public function get(){
        echo 'From FileSystem';
    }
}

class DB_Cache implements Cache { 
    protected $table;
    
    public function __construct(string $table){
        $this->table = $table;
    }
    
    public function maybe_create_table(){
        // Creates table
    }
    
    public function get(){
        echo 'From DB';
    }
}

class My_Service_A{
    
    protected $repository;
    
    public function __construct(Cache $cache){
        $this->cache = $cache;
    }
    
    public function get(){
        return $this->cache->get();
    }
}

class My_Service_B extends My_Service_A{}
```

Here we have 2 classes that implement the Cache interface, but both take different parameters and have methods that need to be called, to ensure they are correctly configured. To ensure this is done, we can define rules which not only pass the parameters but also call the methods.

```php
//@file_location = config/depenecies.php

return array(
  
  // Tell dice to use File_Cache on My_Service_A
	My_Service_A::class => array(
		'substitutions' => array(
				Cache::class => File_Cache::class,
			),
	),
	
	// Construct File_Cache
	File_Cache::class => array(
		// Pass our path and extension to constructor
		'constructParams' => array( 'Some/path', 'do' ),
		// Call the maybe_create_dir() method.
		'call' => array(
			array( 'maybe_create_dir', array(/*No params needed*/), Dice::CHAIN_CALL ),
		),
	),
	
	// Alternatively this can be done as.
	// So long as the method returns itself (return $this;)
	My_Service_A::class => array(
		'substitutions' => array(
				Cache::class => (new File_Cache('some/path', 'do'))->maybe_create_dir(),
			),
	),
);
```

With the 2 examples above, the first will create **File\_Cache** the same on every instance it is called from \(as we set a global rule for it\). Whereas the second will only construct **File\_Cache** in that configuration for the **My\_Service\_A** instances.

### Multiple Method Calls

If your class needs to call various methods for setup, these can be chained easily using.

```php
//@file_location = config/depenecies.php

return array(  
	......
	// Construct File_Cache
	File_Cache::class => array(
		.....
		// Call the maybe_create_dir() method, then flush_existing().
		'call' => array(
			array( 'maybe_create_dir', array(), Dice::CHAIN_CALL ),
			array( 'flush_existing', array(), Dice::CHAIN_CALL ),
		),
	),
	.......
);
```

