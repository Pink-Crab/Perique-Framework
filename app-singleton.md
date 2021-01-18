---
description: >-
  At its heart is the App class, a singleton class which holds our application
  together.
---

# App \(Singleton\)

## Public Methods

### App::init\(\)

> @param PinkCrab\Core\Interfaces\Service\_Container $service\_container  
> @retrun PinkCrab\Core\Application\App

Primary static method to use to initalise the application. Must have a container passed into it, preferably already populated.

### App::get\_instance\(\)

> @retrun PinkCrab\Core\Application\App

Returns the current instance of the App container.

### App::retreive\(\)

> @param string $key The service to recall.  
> @return object The service requested  
> @throws **OutOfBoundsException** If key not set.

Used to recall a service from the App Container statically.

### App::make\(\)

> @param string Class to make.  
> @param array Constructor arguments _\(can be left blank\)_  
> @throws **OutOfBoundsException** If App not initalised  
> _@throws_ **ReflectionException** if class doesnt exist.  
> _@retrun object_

Creates instances \(or retrieves from cache\) of the object requested. This is all run through the DI container, so dependencies will be injected \(see _Dependency Injection_ section for more details\). Either the full namespaced class name should be used, or the _`Foo::class`_ shortcut.   
  
Constructor arguments can be passed as none indexed array, partial arguments may also be supplied with some limitations. For more details, please see the [DICE documentation \(See section 3.3\)](https://r.je/dice).

```php
class Foo {
    public function say(){
        echo 'Hello';
    }
}

$foo = App::make(Foo::class);
$foo->say(); // 'Hello';
```

### App::config\(\)

> @param string $method The App\_Config method to call.  
> @param string ...$args Any args passed to method.  
> @throws OutOfBoundsException If App not initalised  
> @throws ReflectionException if class doesnt exist.  
> @retrun mixed

Direct access to the App\_Config object, should be treated the same as using call\_user\_func\(\). Is not a very clean way of calling, doing App::retreive\(App\_Config::class\)-&gt;url\(\);

### App::\_\_callStatic\(\)

> @param string $key The method called   
> @param array $params Ignored  
> @throws OutOfBoundsException If App not initalised

We have the magic method set to allow access to any service bound to the App Container, using its key. If service `Foo` has be bound, `App::Foo()` would return the current instance.

### set\(\)

> @param string $key The key to bind the object  
> @param object $service The service being bound.  
> @return self

Allows the binding of service to the App Container. The key used must conform to regular PHP naming conventions and the service must be an object.

```php
$app = App::get_instance();
$app->set('my_service', $some_object);
var_dump(App::retreive('my_service')); // $some_object;

// Or
var_dump(App::my_service()); // $some_object;
```

### get\(\)

> @param string $key The service to call  
> @return object The service called.  
> @throws OutOfBoundsException If key not set.



```php
$app = App::get_instance();
$app->set('my_service', $some_object');
var_dump($app->get('my_service')); // $some_object;
```



