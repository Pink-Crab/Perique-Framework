---
description: >-
  We can construct objects which have a single defined value it can be. This
  allows the injection of dependencies within objects.
---

# Basic Usage

```php
class Repository{
    
    public function get(){
        echo 'test from repository';
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

My service can be constructed using the DI container by doing the following.

```php
$service = App::make(My_Service::class);

// We can then access the repository from our service.
$service->get(); // test from repository

// It could also be constructed with a custom repository (so long as the type matches) 
$service = App::make(My_Service::class, [new Custom_Repository()]);
```

Obviously, this can not be done if the object requires defined values.

```php
class My_Service{
    
    protected $account;
    
    public function __construct(int $account_id){
        $this->account = get_account($account_id);
    }
}

// This would have to be constructed using either.
App::make(My_Service::class, [123]);
//or
new My_Service(123);
```

The beauty of using the DI container, is we can cache the instances.

