---
description: >-
  WordPress development involves lots of keys, slugs, namespaces and paths, lots
  of them. App_Config gives an injectable container for holding your keys.
---

# App\_Config

### Setup

**App\_Config** is constructed and bound to the **Apps Container** at initalisation. A set of defaults are set for wp\_upload paths, but these can be overwritten. All custom values can be set in the config/settings.php file \(this location can be anywhere, based on your setup\)

```php
// file - config/settings.php

// Get WP and current path.
$base_path  = \dirname( __DIR__, 1 );
$plugin_dir = \basename( $base_path );
$wp_uploads = \wp_upload_dir();

return array(
	 	'plugin'     => array(
	 		'version' => '0.1.0',
	 	),
	 	'path'       => array(
	 		'plugin'         => $base_path,
	 		'view'           => $base_path . '/views',
	 		'assets'         => $base_path . '/assets',
	 		'upload_root'    => $wp_uploads['basedir'],
	 		'upload_current' => $wp_uploads['path'],
	 	),
	 	'url'        => array(
	 		'plugin'         => plugins_url( $plugin_dir ),
	 		'view'           => plugins_url( $plugin_dir ) . '/views',
	 		'assets'         => plugins_url( $plugin_dir ) . '/assets',
	 		'upload_root'    => $wp_uploads['baseurl'],
	 		'upload_current' => $wp_uploads['url'],
	 	),
	 	'post_types' => array(),
	 	'taxonomies' => array(),
	 	'db_tables'  => array(),
	 	'namespaces' => array(
	 		'rest'  => 'pinkcrab',
	 		'cache' => 'pc_cache',
	 	),
	 	'additional' => array(),
);
```

{% hint style="info" %}
If you do not wish to change any of the path or url details, you can remove them from your settings.php file, as they will be added.
{% endhint %}

### Paths

The values set by default \(plugin, view, assets, upload\_root & upload\_current\) are the only values that can be called. Any additional keys added, will be stripped, use Additional for any extra values.

Paths can either be retireved as a full array, or by key.

```php
	/**
	 * Gets a path with trailing slash.
	 *
	 * @param string|null $path
	 * @return array<string, mixed>|string|null
	 */
	public function path( ?string $path = null )
```

* If called with no arguemnts `path()`, will return the paths array. 
* If called with any of the defined keys `path('view')` will return the full path

```php
// Via Dependency Injection 
 class Foo {
    protected $config;
    public function __constuct(App_Config $config){
        $this->config = $config;
    }
    public function something(){
        $this->config->path(); // all paths in array
        $this->config->path('plugin'); // "/path/to/wp-content/plugins/my-plugin/"
        $this->config->path('upload_root'); // "/path/to/wp-content/uploads/"
    }
}

// Via Config Proxy Class
Config::path(); // all paths in array
Config::path('plugin'); // "/path/to/wp-content/plugins/my-plugin/"
Config::path('upload_root'); // "/path/to/wp-content/uploads/"

// Via App Singleton
App::config('path'); // all paths in array
App::config('path','plugin'); // "/path/to/wp-content/plugins/my-plugin/"
App::config('path','upload_root'); // "/path/to/wp-content/uploads/"

```

### URLs

The values set by default \(plugin, view, assets, upload\_root & upload\_current\) are the only values that can be called. Any additional keys added, will be stripped, use Additional for any extra values.

Paths can either be retrieved as a full array or by key.

```php
	/**
	 * Gets a path with trailing slash.
	 *
	 * @param string|null $path
	 * @return array<string, mixed>|string|null
	 */
	public function url( ?string $path = null )
```

* If called with no arguments `url()`, will return the paths array. 
* If called with any of the defined keys `url('plugin')` will return the full path

```php
// Via Dependency Injection 
 class Foo {
    protected $config;
    public function __constuct(App_Config $config){
        $this->config = $config;
    }
    public function something(){
        $this->config->url(); // all urls in array
        $this->config->url('assets'); // "https://url.com/wp-content/plugins/my-plugin/assets/"
        $this->config->url('view'); // "https://url.com/wp-content/plugins/my-plugin/views/"
    }
}

// Via Config Proxy Class
Config::url(); // all urls in array
Config::url('assets'); // "https://url.com/wp-content/plugins/my-plugin/assets/"
Config::url('view'); // "https://url.com/wp-content/plugins/my-plugin/views/"

// Via App Singleton
App::config('url'); // all urls in array
App::config('url','assets'); // "https://url.com/wp-content/plugins/my-plugin/assets/"
App::config('url','view'); // "https://url.com/wp-content/plugins/my-plugin/views/"
```

{% hint style="info" %}
Returns null if the key passed doesn't exist.
{% endhint %}

### Namespaces

Out of the box only cache and rest are defined \(and come with helper methods\), if not defined will be set as **rest = pinkcrab** and **cache = pc\_cache.** As many additonal key & value pairs can be added and accessed using the `namespace( $key )` method.

```php
/**
	 * Return a namespace by its key.
	 *
	 * @param string $key
	 * @return string|null
	 */
	public function namespace( string $key ): ?string
```

Can only be called with a key, but will just return null if the key is not defined.

```php
// file - config/settings.php
return array(
    ....
	 	'namespaces' => array(
	 		'rest'        => 'my_plugin',
	 		'cache'       => 'file_cache',
	    'some_prefix' => 'dcv_'
	 	),
);

// Useage

// Via Dependency Injection 
 class Foo {
    protected $config;
    public function __constuct(App_Config $config){
        $this->config = $config;
    }
    public function something(){
        $this->config->namespace('rest'); // "my_plugin"
        $this->config->namespace('some_prefix'); // "dcv_"
    }
}

// Via Config Proxy Class
Config::namespace('rest'); // "my_plugin"
Config::namespace('some_prefix'); // "dcv_"

// Via App Singleton
App::config('namespace','rest'); // "my_plugin"
App::config('namespace','some_prefix'); // "dcv_"
```

#### cache\(\) & rest\(\) helpers

Both cache and rest have helpers, these require no arguments and can be called as.

```php
// Via Dependency Injection 
 class Foo {
    protected $config;
    public function __constuct(App_Config $config){
        $this->config = $config;
    }
    public function something(){
        $this->config->rest(); // "my_plugin"
        $this->config->cache(); // "file_cache"
    }
}

// Via Config Proxy Class
Config::rest(); // "my_plugin"
Config::cache(); // "file_cache"

// Via App Singleton
App::config('rest'); // "my_plugin"
App::config('cache'); // "file_cache"
```

### DB\_Tables

A simple key =&gt; value set can be added for database tables, allowing for dynamic values. As many values can be added and called out via their key. 

```php
/**
	 * Returns a table name based on its key.
	 *
	 * @param string $name
	 * @return string
	 * @throws OutOfBoundsException
	 */
	public function db_tables( string $name ): string
```

If a key is called which is not defined, it will throw and OutOfBoundsException.

```php
// file - config/settings.php
return array(
    ....
	 	'db_tables' => array(
	 		'cache'     => 'my_plugin_cache',
	 		'email_log' => 'my_plugin_email_log',
	 	),
);

// Useage

// Via Dependency Injection 
 class Foo {
    protected $config;
    public function __constuct(App_Config $config){
        $this->config = $config;
    }
    public function something(){
        $this->config->db_tables('cache'); // "my_plugin_cache"
        $this->config->db_tables('email_log'); // "my_plugin_email_log"
    }
}

// Via Config Proxy Class
Config::db_tables('cache'); // "my_plugin_cache"
Config::db_tables('email_log'); // "my_plugin_email_log"

// Via App Singleton
App::config('db_tables','cache'); // "my_plugin_cache"
App::config('db_tables','email_log'); // "my_plugin_email_log"
```

### Post Types

**Post Types** are added with both a slug and meta keys, both of which must be set or and OutOfBoundsException will be thrown at run time.

```php
return [
    ....
    'post_types' => [
        // Events
        'events' => [ 
            // Allows expressions for values.
            'slug' => get_option( 'rjc_event_cpt_slug', 'rjc_events' ),
            'meta' => [
                'date' => 'rjc_event_date',
                'location' => 'rjc_location_id'
            ]
        ]
    ],
    ....
];
```

Now anywhere in our code, we can access these values using the post\_type\(\) 

```php
/**
 * Returns the key for a post type.
 *
 * @param string $key
 * @return string|array
 * @throws OutOfBoundsException
 */
public function post_types( string $key, string $field = 'slug', ?string $meta_key = null )
```

Whatever key you set your slug and meta array, will be the key used to access it. 

* Calling with just the key `post_type( {$key} )`; will return the slug value \(as will calling `post_type( {$key},  'slug' )`;
* Calling with the key and just meta as the field value `post_type( {$key},  'meta' )` will return the array of all meta key values.
* Calling with the key, meta, and the meta\_key will return your value. `post_type( {$key},  'meta', {$meta_key} )`

```php
// Via Dependency Injection
class Foo {
    protected $config;
    
    public function __constuct(App_Config $config){
        $this->config = $config;
    }
    
    public function something(){
        $this->config->post_type('events'); // "rjc_events"
        $this->config->post_type('events', 'slug' ); // "rjc_events"
        $this->config->post_type('events', 'meta' ); // [ 'date' => 'rjc_event_date', 'location' => 'rjc_location_id' ]
        $this->config->post_type('events', 'meta', 'date' ); // "rjc_event_date"
        $this->config->post_type('events', 'meta', 'location' ); // "rjc_location_id"
    }
}

// Via Config Proxy Class
Config::post_type('events'); // "rjc_events"
Config::post_type('events', 'slug' ); // "rjc_events"
Config::post_type('events', 'meta' ); // [ 'date' => 'rjc_event_date', 'location' => 'rjc_location_id' ]
Config::post_type('events', 'meta', 'date' ); // "rjc_event_date"
Config::post_type('events', 'meta', 'location' ); // "rjc_location_id"

// Via App Singleton
App::config('post_type','events'); // "rjc_events"
App::config('post_type','events', 'slug' ); // "rjc_events"
App::config('post_type','events', 'meta' ); // [ 'date' => 'rjc_event_date', 'location' => 'rjc_location_id' ]
App::config('post_type','events', 'meta', 'date' ); // "rjc_event_date"
App::config('post_type','events', 'meta', 'location' ); // "rjc_location_id"  
```

{% hint style="info" %}
OutOfBoundsException will be thrown if a post type or meta key is called, that doesnt exist.
{% endhint %}

### Taxonomies

**Taxonmies** are added with both a slug and term keys, both of which must be set or and OutOfBoundsException will be thrown at run time. You can set common term 

```php
return [
    ....
    'taxonmies' => [
        // Events
        'event_type' => [ 
            // Allows expressions for values.
            'slug' => get_option( 'rjc_event_type_tax_slug', 'rjc_event_type' ),
            'term' => [
                'advanced' => 'advanced_only',
                'featured' => 'featured_event'
            ]
        ]
    ],
    ....
];
```

Now anywhere in our code, we can access these values using the taxonmies\(\) 

```php
/**
 * Returns keys for taxonomies.
 *
 * @param string $key
 * @param string $field
 * @param string $term_key
 * @return string|array
 * @throws OutOfBoundsException
 */
public function taxonomies( string $key, string $field = 'slug', ?string $term_key = null )
```

Whatever key you set your slug and meta array, will be the key used to access it. 

* Calling with just the key `post_type( {$key} )`; will return the slug value \(as will calling `post_type( {$key},  'slug' )`;
* Calling with the key and just meta as the field value `post_type( {$key},  'meta' )` will return the array of all meta key values.
* Calling with the key, meta, and the meta\_key will return your value. `post_type( {$key},  'meta', {$meta_key} )`

```php
// Via Dependency Injection
class Foo {
    protected $config;
    
    public function __constuct(App_Config $config){
        $this->config = $config;
    }
    
    public function something(){
        $this->config->post_type('events'); // "rjc_events"
        $this->config->post_type('events', 'slug' ); // "rjc_events"
        $this->config->post_type('events', 'meta' ); // [ 'date' => 'rjc_event_date', 'location' => 'rjc_location_id' ]
        $this->config->post_type('events', 'meta', 'date' ); // "rjc_event_date"
        $this->config->post_type('events', 'meta', 'location' ); // "rjc_location_id"
    }
}

// Via Config Proxy Class
Config::post_type('events'); // "rjc_events"
Config::post_type('events', 'slug' ); // "rjc_events"
Config::post_type('events', 'meta' ); // [ 'date' => 'rjc_event_date', 'location' => 'rjc_location_id' ]
Config::post_type('events', 'meta', 'date' ); // "rjc_event_date"
Config::post_type('events', 'meta', 'location' ); // "rjc_location_id"

// Via App Singleton
App::config('post_type','events'); // "rjc_events"
App::config('post_type','events', 'slug' ); // "rjc_events"
App::config('post_type','events', 'meta' ); // [ 'date' => 'rjc_event_date', 'location' => 'rjc_location_id' ]
App::config('post_type','events', 'meta', 'date' ); // "rjc_event_date"
App::config('post_type','events', 'meta', 'location' ); // "rjc_location_id"  
```

{% hint style="info" %}
OutOfBoundsException will be thrown if a post type or meta key is called, that doesnt exist.
{% endhint %}

