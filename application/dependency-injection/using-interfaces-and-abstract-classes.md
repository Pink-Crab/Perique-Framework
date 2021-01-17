---
description: >-
  The full power comes with the use of interfaces and abstract classes as
  dependencies.
---

# Using Interfaces and Abstract Classes

```php
interface Repository{
    public function get();
}

class Repository_A implements Repository { 
    public function get(){
        echo 'From A';
    }
}

class Repository_B implements Repository { 
    public function get(){
        echo 'From B';
    }
}

class My_Service{
    
    protected $repository;
    
    public function __construct(Repository $repository){
        $this->repository = $repository;
    }
    
    public function get(){
        return $this->repository->get();
    }
}
```

If we tried to construct My\_Service, we would get an error as Dice doesn't know what class to use to implement the Repository interface.   
To define this, we have a few different rules we can define in the `config/dependecies.php` file.

#### Setting a catch-all rule.

A global rule can be set so that whenever **Repository** is passed as a dependency, we always return a specific class that implements the interface.

```php
//@file_location = config/depenecies.php

return array(
  
  // Set a rule to always be used when DICE encounters the Repository interface.
	Repository::class => array(
		'instanceOf' => Repository_B::class
	), 
	
);
```

Now we can easily create the `My_Service` object using the container and will always be passed `Reposiory_B`

```php
$service = App::make(My_Service::class);
$service->get(); // 'From B'
```

#### Class by Class Rule.

Along with global rules, we can specify a class to implement the interface on a class by class basis.

```php
//@file_location = config/depenecies.php

return array(
  
  // Fallback
	Repository::class => array(
		'instanceOf' => Repository_B::class
	),
	
	
  // Ensure My_Service is constructed with Repository_A
	My_Service::class => array(
		'substitutions' => array(
			Repository::class => Repository_A::class
		),
	),
	
);
```

Now whenever we create an instance of **My\_Service** we will be given an instance of **Repository\_B**, but any other Class which is passed **Repository** as a dependency will still get an instance of **Repository\_A**

```php
$service = App::make(My_Service::class);
$service->get(); // 'From A'
$other_service = App::make(My_Other_Service::class);
$other_service->get(); // 'From B'
```

Not only can you pass just class names here you can also pass fully instanced objects as parameters. Allowing for custom instances on a class by class basis

```php
//@file_location = config/depenecies.php

return array(
  
  // Ensure My_Service is constructed with Repository_A
	Some_Service::class => array(
		'substitutions' => array(
			Cache::class => new File_Cache('some/path/for/cache/location')
		),
	),
	
	Some_Other_Service::class => array(
		'substitutions' => array(
			Cache::class => new File_Cache('some/other/path/for/cache/location')
		),
	),
	
);
```

