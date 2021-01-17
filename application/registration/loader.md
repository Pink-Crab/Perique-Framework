---
description: >-
  The PinkCrab loader allows for actions, filters, shortcodes and Ajax calls to
  be hooked into wordpress.
---

# Loader

## Actions

The PinkCrab loader has 3 action methods;

* action\(\)
* admin\_action\(\)
* front\_action\(\)

These can be used to ensure your action is only registered at the correct time.

As with the native add\_action\(\) function, these take the same parameters in the same order.

> $loader-&gt;action\('hook', 'callback', 'priority', 'args'\)

As with the native add\_action functionality, priority defaults to 10 and argument count to 1.

### Loader::action\(\)

> @param string $handle Hook name/handle  
> @param callable $method The function/method to call.  
> @param int $priority The priority of the hook call. \(Defaults to 10\)  
> @param int $args The argument count \(Defulats to 1\)  
> @return void

The regular action\(\) method is called regardless of if being called in wp-admin, ajax, rest or the front end. 

```php
$loader->action(
    'some_action', 
    function($foo, $bar){
        print('I will be printed regardless of where im called'); 
    },
    10,
    2
);
```

### Loader::admin\_action\(\)

> @param string $handle Hook name/handle  
> @param callable $method The function/method to call.  
> @param int $priority The priority of the hook call. \(Defaults to 10\)  
> @param int $args The argument count \(Defulats to 1\)  
> @return void

The admin\_action\(\) method will ensure that the hook is only added if you are calling this from either wp\_admin, ajax, or rest.

This is useful for some WooComerce hooks where the same hook is fired front and back but might cause conflicts if fired on the front end.

```php
$loader->admin_action(
    'some_admin_action', 
    function($foo, $bar, $baz){
        print('I will be printed if called in wp-admin, ajax callback or rest endpoint.'); 
    },
    11,
    3
);
```

### Loader::front\_action\(\)

> @param string $handle Hook name/handle  
> @param callable $method The function/method to call.  
> @param int $priority The priority of the hook call. \(Defaults to 10\)  
> @param int $args The argument count \(Defulats to 1\)  
> @return void

The front\_action hook allows for hooking into front end hook calls only. 

```php
$loader->front_action(
    'wp_footer', 
    function(){
        print('<p>Im in the footer!</p>'); 
    }
);
```

## Filters

The PinkCrab loader has 3 filter methods;

* filter\(\)
* admin\_action\(\)
* front\_filter\(\)

These work just the same as the action methods above, same arguments and defualts.

### Loader::filter\(\)

> @param string $handle Hook name/handle  
> @param callable $method The function/method to call.  
> @param int $priority The priority of the hook call. \(Defaults to 10\)  
> @param int $args The argument count \(Defulats to 1\)  
> @return void

The regular filter\(\) method is called regardless of if being called in wp-admin, ajax, rest or the front end. 

```php
$loader->filter(
    'super_special_post_access', 
    function($users){
        return array_filter($users, function($user){
            return in_array('custom_role', (array) $user->roles, true);
    },
    99999
);
```

### Loader::admin\_filter\(\)

> @param string $handle Hook name/handle  
> @param callable $method The function/method to call.  
> @param int $priority The priority of the hook call. \(Defaults to 10\)  
> @param int $args The argument count \(Defulats to 1\)  
> @return void

The admin\_action\(\) method will ensure that the hook is only added if you are calling this from either wp\_admin, ajax, or rest.

This is useful for some WooComerce hooks where the same hook is fired front and back but might cause conflicts if fired on the front end.

```php
$loader->admin_filter(
    'manage_edit-post_columns', 
    function(array $columns, int $post_id): array{
        // Show a custom title in wp-admin list tables.
        $columns['title'] = sprintf(
            '%s(%s)', 
            $columns[$title], 
            get_post_meta($post_id, 'some_meta_key', true)
        );
        return $columns;
    },
    11,
    2
);
```

### Loader::front\_filter\(\)

> @param string $handle Hook name/handle  
> @param callable $method The function/method to call.  
> @param int $priority The priority of the hook call. \(Defaults to 10\)  
> @param int $args The argument count \(Defulats to 1\)  
> @return void

The front\_filter hook allows for hooking into front end hook calls only. 

```php
$loader->front_filter(
    'the_title', 
    function(string $title, int $post_id): string{
        return (bool) get_post_meta($post_id, 'requires_attention', true) 
            ? "{$title} <span class='notice'>Notice Me!</span>"
            : $title;
    }, 10, 2
);
```



