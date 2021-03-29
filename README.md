# PinkCrab Framework Core #

Welcome the main package of the PinkCrab Framwework. 

![alt text](https://img.shields.io/badge/Current_Version-0.4.0-yellow.svg?style=flat " ") 
[![Open Source Love](https://badges.frapsoft.com/os/mit/mit.svg?v=102)]()
![](https://github.com/Pink-Crab/Framework__core/workflows/GitHub_CI/badge.svg " ")
[![codecov](https://codecov.io/gh/Pink-Crab/Framework__core/branch/master/graph/badge.svg?token=VW566UL1J6)](https://codecov.io/gh/Pink-Crab/Framework__core)


For more details please visit our docs.
https://app.gitbook.com/@glynn-quelch/s/pinkcrab/


## Version 0.4.0 ##


## Why? ##
WordPress is powerful tool for building a wide range of website, but due to its age and commitment to backwards compatibility. Its often fustrating to work with using more modern tools. 

The PinkCrab Framework allows the creation of Plugins, Themes and MU Libraries for use on more complex websites.

The Core only provides access to the Loader, Registration, Collection, DI (DICE Dependency Injection Container), App_Config and basic (native) PHP render engine for view.

## Setup ##

```bash 
$ composer require pinkcrab/plugin-framework 
```

*new setup for v0.4.0 and above*

First you will need to create your composer.json and plugin.php file. 

### plugin.php ###

````php
// @file plugin.php 
<?php
     
/**
 * @wordpress-plugin
 * Plugin Name:     ##PLUGIN NAME##
 * Plugin URI:      ##YOUR URL##
 * Description:     ##YOUR PLUGIN DESC##
 * Version:         ##VERSION##
 * Author:          ##AUTHOR##
 * Author URI:      ##YOUR URL##
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     ##TEXT DOMAIN##
 */

require_once __DIR__ . '/vendor/autoload.php';

// Creates an App loaded with the WP_Dice DI container and basic DI rules
// Allows for the passing of wpdb and the App's own instance.
$app = ( new App_Factory )->with_wp_dice( true );

// Set rules and configure DI Container
$app->container_config(function(DI_Container $container): void {
	// Pass an array of rules
	$container->addRules(include __DIR__ . '/config/dependencies.php');
});

// Pass settings for App_Config
$app->app_config( include __DIR__ . '/config/settings.php' )

// Pass all class names which should be used during registration
$app->registration_classses(include __DIR__ . '/config/registration.php' );

// Add custom Regisration Middleware
$app->registration_middleware(new Rest_Route_Registration_Middleware('my_base/route'));

// Then can just boot the application.
$app->boot();

````
## Config files ##

While you can pass arrays to the container_config(), app_config() and registration_classes(), these can get quite large. So its best to have them returned from 

> These files can be placed anywhere, but in the above example and our boilerplates, these 3 files are placed in the /config directory.

### dependencies.php ###

Used to define all of your custom rules for Dice, for more details on how to work with Interfaces and other classes which cant be autowired, see the full docs @todo link

>Using the full class name is essential, so ensure you include all needed use statements.

````php
// @file config/dependencies.php

<?php

use Some\Namespace\{Some_Interface, Some_Implementation};

return array(
    // Your custom rules
	Some_Interface::class => array(
		'instanceOf' => Some_Implementation::class
	)
);
````

### registration.php ###

When the app is booted, all classes which have either hook calls or needed to be called, are passed in this array. 

By default the Registerable middleware is passed, so all classes which implement the Registerable interface will be called. Adding custom Registration Middleware will allow you to pass them in this array for intialisation at boot.

>Using the full class name is essential, so ensure you include all needed use statements.

````php
// @file config/registration.php

<?php

use Some\Namespace\Some_Controller;

return array(
    Some_Controller::class
);
````
### settings.php ###

The App holds an internal config class, this can be used as an injectable collection of helper methods in place of defining lots of constants.

Along side the usual path and url values that are needed frequently. You can also set namesapces (rest, cache), post types (meta and slug), taxonomies (slug & termmeta), database table names and custon values. 
````php
// @file config/settings.php
<?php
    
// Assumes the base directory of the plugin, is 1 level up.
$base_path  = \dirname( __DIR__, 1 );
$plugin_dir = \basename( $base_path );

// Useful WP helpers
$wp_uploads = \wp_upload_dir();
global $wpdb;

return array(
    'plugin'     => array(
		'version' => '1.2.5',
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
	'db_table' => array(
		'subscriptions' => $wpdb->table_prefix . 'some_plugin_subscribers'
	),
    'additional' => array(
		// Custom values go here (Config::additiona('key'); = value)
	),
);
````
> For the full set of options can be found in the [docs](https://app.gitbook.com/@glynn-quelch/s/pinkcrab/application/app_config).


## Registration Service ##

At the heart of the Application is the registration process. Classes can be stacked up and executed at initalisation, this allows for registering into core WP apis, triggering remote api calls and anything else which needs to be setup when all of WP's core is loaded.

### Registerable ###

Included in this framework is a single peice of Registration_Middleware. The Renderable interface and Renderable_Middleware pair make it easy to register any hooks, shortcodes, post types, taxonomies, admin pages, rest endpoints. Any class which needs to be processed, implements the Renderable interface and creates the ```function register(Loader $loader): void {...}```
```php
class Some_Controller implements Registerable {
	public function register(Loader $loader): void{
		$loader->admin_action('some_action', [$this, 'some_action']);
	}
	public function some_action($some_arg): void {...}
}
```
Now when the init hook is called (priority 1), the some_action hook will be added. So long as the request comes from wp-admin. 

> For more details on Registerable and the Hook Loader please see the full docs

### Registration Middleware ###

Custom registration processes can be added using Registration_Middleware, you can easily create your own middleware that implements the ```PinkCrab\Core\Interfaces\Registration_Middleware``` interface. This interface consists of a single method ```process(object $class): void``` which is passed each class.

```php
<?php

class Does_Something implements PinkCrab\Core\Interfaces\Registration_Middleware {

	/** @var Some_Service */
	protected $some_service;
	
	public function __cosntruct(Some_Service $some_service){
		$this->some_service = $some_service;
	}

	public function process(object $class): void {
		// Use interfaces or abstract classes to ensure you only process classes you expected
		if ( in_array( Some_Interface::class, class_implements( $class ) ?: array(), true ) ) {
			$this->some_service->so_something($class);
		}
	}
}
```
> The objects are passed fully cosntructed using the DI_Container

You can then pass these custom Registatration_Middlewares to the app at boot.

```php
<?php 

$app = ( new App_Factory )->with_wp_dice( true )
	// Rest of bootstrapping
	->registration_middleware(new Does_Something(new Some_Service()))
	->boot();
```


## Static Helpers ##

The App object has a few helper methods, which can be called statically (either from an instance, or from its name). 

### App::make(string $class, array $args = array()): object ###
* @param string $class Fully namespaced class name
* @param array<string, mixed> $args Constcutor params if needed
* @return object Object instance
* @throws App_Initialization_Exception Code 4 If app isnt intialised.

```make()``` can be used to access the Apps DI Container to fully resuolve the depenecies of an object. 

```php 
$emailer = App::make(Customer_Emailer::class);
$emailer->mail(ADMIN_EMAIL, 'Some Report', $email_body);
$emailer->send();
```

### App::config(string $key, ...$child): mixed ###
* @param string $key The config key to call
* @param ...string $child Additional params passed.
* @return mixed
* @throws App_Initialization_Exception Code 4 If app isnt intialised.

Once the app has been booted, you can access the App_Config values by either passing App_Config as a dependency, or by using the Apps helper.

```php

// Get post type slug
$args = ['post_type' => App::config('post_types', 'my_cpt')];

// Get current plugin version.
$version = App::config('version');
```

> For more details on App_Config and its various usecases, [please checkout the full docs](https://app.gitbook.com/@glynn-quelch/s/pinkcrab/application/app_config).

### App::view(): View ###
* @return View
* @throws App_Initialization_Exception Code 4

If you need to render or return a template, you can use the ```view()``` helper. Returns an instance of the View class, populated with the current defined engine (use PHP by default).

```php
App::view()->render('signup/form', ['user' => wp_get_current_user(), 'nonce' => $nonce]);
```

> While the View and Config helpers are useful at times, its always better to inject them (App_Config::class or View::class).

## Hooks ##

We have a number of hooks you can use to extend or modify how the app works. All of our internal hooks have pinkcrab/pf/app/ prefix, but we have a class of constants you can use ```PinkCrab\Core\Application\Hooks::class```

### Hooks::APP_INIT_PRE_BOOT ###
This is primarily used internally to make last minute changes to how the boot process works. Due to the way this hook is used (called when plugin.php is loaded) it should not be used from outside of your own code, as you can be 100% external code will load first.

```php
add_action( Hooks::APP_INIT_PRE_BOOT, function( App_Config $app_config, Loader $loader, DI_Container $container ): void {
	// do something cool
});
```

## License ##

### MIT License ###
http://www.opensource.org/licenses/mit-license.html  

## Update Log ##
* 0.4.0 - Introduced new app, with app factory to help with cleaner initalisation. Reintroduced Registation_Middleware which was removed in 0.2.0. Moved the registerables into a default piece of middleware which is automatically added at boot. Added a series of actions around the init callback which runs the registation process.
* 0.3.9 - Moved Loader into its own library, all tests and use statements updated.
* 0.3.8 - Added in missing Hook_Removal & Loader tests.
* 0.3.7 - Added in Hook_Removal and made minor changes to the Loader tests.
* 0.3.6 - Added remove_action() and remove_filter() to Loader
* 0.3.5 - Added coverage reports to gitignore
* 0.3.4 - Improved tests and hooked to codecov
* 0.3.3 - Removed object type hint from service container.
* 0.3.2 - Added in tests and expanded view
* 0.3.1 - Minor docblock changes for phpstan lv8
