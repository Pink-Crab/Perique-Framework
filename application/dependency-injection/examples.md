---
description: >-
  The use of dependency injection and Registration services, allows you to
  create clean and concise code.
---

# Examples

This class has access to a repository for getting data from a db, filesystem, remote api and also access to a custom cache instance.

```php
class My_Service implements Registerable {
    protected $repository;
    protected $cache;
    
    public function __construct(Repository $repository, Cache $cache){
        $this->cache = $cache;
        $this->repository = $repository;
    }
    
    /**
     * Allow our class to register hook calls via the loader.
     */
    public function register( Loader $loader ): void{
        $loader->filter(
            'ache_users', 
            function($users){
                return array_merge( $users, $this->get_users();
            }
        );
    }
    
    /**
     * Get users from either cache, or from repository if cache not valid.
     */
    protected function get_users(): array {
        $users = $cache->get('users', null);
        if( ! $users ){
            $users = $this->repository->get_users();
            $this->cache->set('users', $users, 1 * HOURS_IN_SECONDS);
        }
        
        reutrn $users ?? [];
    }
}
```

We need to declare a few rules for the dependency injection and also to ensure **My\_Service** is added to the registration process.

```php
//@file_location = config/depenecies.php

return array(
    .....
    My_Service::class => array(
        'substitutions' => array(
            Cache::class => new File_Cache('my/user/cache/path', '.do'),
            Repository::class => User_Repository::class
        ),
    ),
    .....
);
```

```php
//@file_location = config/registration.php

return array(
    ......
    My_Service::class,
    ......
);
```

> When declaring a class to be registered, ensure you either use the full namespaced class name `'MySite\Posts\Post_Repository'` or with `Post_Repository::class` with `use MySite\Posts\Post_Repository;`

Now when the App is initialised, MyService will be constructed using the DI container and have **Loader** passed into the service on the **register\(\)** method. Then once we are ready to register all, our hooks will be added and we have full access to our **File\_Cache** and **User\_Repository** within our class.

```php
$users = apply_filters('ache_users', []);
dump(count($users)); // 5 (assuming 5 are added from My_Service)
```

Testing

When it comes to testing, you can create a mock Repository, allowing you to test other aspects of your code.

```php
// The mock users repository.
// Always returns the same users
class Mock_User_Repository implements Repository{
    public function get_users(){
        return [
            ['id' => 1, 'name' => 'Jo'],
            ['id' => 2, 'name' => 'Sam']
        ];
    }
}

// The mock file cache instance.
// Constrcutor allows for setting the cache payload, so you can mock its values.
class Mock_File_Cache implements Cache{
    
    public $cache_contents;
    
    
    public function __construct($value){
        $this->cache_contents = $value;
    }
    
    public function set($key, $value, $expiry){
        return true;
    }
    
    public function get($key, $fallback){
        return $this->cache_contents;
    }
}

// Then in your tests.
use PinkCrab\Core\Services\Registration\Loader;

class Test_My_Service extends WC_Unit_Test_Case {

    // Create the mocked instance of our service
    // register its actions and test.
    public function test_ache_users_filters(){
    
        // Create the service.
        $my_service = new My_Service(
            new Mock_User_Repository(),
            new Mock_File_Cache(null) 
            // Returns null from cache, so calls from user repository is used.
        );
        
        // Create new instance of Loader, do not use Loader::boot() in tests.
        $loader = new Loader();
        
        // Add all hooks & register them.
        $my_service->register($loader);
        $loader->register_hooks();
        
        // We can then test our filter has been registered.
        $users = apply_filters('ache_users', ['id' => 999, 'name' => 'Jane']);
        $this->assertEquals('Jane', $users[0]['name']);
        $this->assertEquals('Jo', $users[1]['name']);
        $this->assertEquals('Sam', $users[2]['name']);
    }
}

```

