# PinkCrab **Perique** Plugin Framework #

Welcome to the core package of the PinkCrab **Perique** plugin framework, formally known as just the PinkCrab Plugin Framework. 

![alt text](https://img.shields.io/badge/Current_Version-1.0.8-yellow.svg?style=flat " ") 
[![Open Source Love](https://badges.frapsoft.com/os/mit/mit.svg?v=102)]()
![](https://github.com/Pink-Crab/Perqiue-Framework/workflows/GitHub_CI/badge.svg " ")
[![codecov](https://codecov.io/gh/Pink-Crab/Perqiue-Framework/branch/master/graph/badge.svg?token=yNsRq7Bq1s)](https://codecov.io/gh/Pink-Crab/Perqiue-Framework)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Pink-Crab/Perqiue-Framework/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Pink-Crab/Perqiue-Framework/?branch=master)

For more details please visit our docs.
https://perique.info

## Version 1.0.8 ##

## Why? ##

WordPress is a powerful tool for building a wide range of websites, but due to its age and commitment to backwards compatibility it's often frustration to work with using more modern tools.

Perique allows the creation of plugins, themes and MU libraries for use on more complex websites.

The Core only provides access to the Hook_Loader, Registration, DI (DICE Dependency Injection Container), App_Config and basic (native) PHP render engine for view.

## What is Perique? ##

Perique is rare form of pipe tobacco produced in the St James Parish of Louisiana. This historic tobacco has been produced in the region for centuries and sees tobaccos taken, packed into a barrels under pressure and left to ferment for over 12 months. The resulting tobacco has a strong and pungent quality, which is used to heavily enhance a tobaccos flavour, nicotine content and aroma with only a small quantity used. This is something we strived to produce in this framework; a small amount of existing code that can be used to enhance any codebase to be big, bold and striking.

## Setup ##

```bash 
$ composer require pinkcrab/perique-framework-core

```

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
$app = ( new PinkCrab\Perique\Application\App_Factory )->with_wp_dice( true );

// Set rules and configure DI Container
$app->di_rules(include __DIR__ . '/config/dependencies.php');

// Pass settings for App_Config
$app->app_config( include __DIR__ . '/config/settings.php' )

// Pass all class names which should be used during registration
$app->registration_classes(include __DIR__ . '/config/registration.php' );

// Add custom Registration Middleware (Not usually used!!! OPTIONAL!)
$app->registration_middleware(new Example_Rest_Route_Registration_Middleware('my_base/route'));

// Then just boot the application.
$app->boot();

````

## Config files ##

While you can pass arrays to the container_config(), app_config() and registration_classes(), these can get quite large. It can help return them from files.

> These files can be placed anywhere, but in the above example and our boilerplate's, these 3 files are placed in the /config directory.

### dependencies.php ###

Used to define all of your custom rules for Dice, for more details on how to work with Interfaces and other classes which cant be autowired, see the [full docs ](https://app.gitbook.com/@glynn-quelch/s/pinkcrab/application/dependency-injection)

> Using the full class name is essential, so ensure you include all needed use statements.

`

```php
// @file config/dependencies.php

<?php

use Some\Namespace\Some_Controller;

return array(
    // Your custom rules
	Some_Interface::class => array(
		'instanceOf' => Some_Implementation::class
	)
);
````

### registration.php ###

When the app is booted, all classes which have either hook calls or needed to be called, are passed in this array. 

By default the Hookable middleware is passed, so all classes which implement the Hookable interface will be called. Adding custom Registration Middleware will allow you to pass them in this array for initialisation at boot.

> Using the full class name is essential, so ensure you include all needed use statements.

`

```php
// @file config/registration.php

<?php

use Some\Namespace\{Some_Interface, Some_Implementation};

return array(
    Some_Controller::class
);
````

### settings.php ###

The App holds an internal config class, this can be used as an injectable collection of helper methods in place of defining lots of constants.

Alongside the usual path and url values that are needed frequently. You can also set namespaces (rest, cache), post types (meta and slug), taxonomies (slug & term meta), database table names and custom values. 
`

```php
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
		// Custom values go here (Config::additional('key'); = value)
	),
);
```

> The full set of options can be found in the [docs](https://app.gitbook.com/@glynn-quelch/s/pinkcrab/application/app_config).

## Registration Service ##

At the heart of the application is the registration process. Classes can be stacked up and executed at initialisation. This allows for registering into core WP APIs, triggering remote API calls and anything else which needs to be set up when all of WP core is loaded.

### Hookable ###

> The `Loader::class` loader has been deprecated and replaced with the new `Hook_Loader::class`

Included with Perique is a single piece of Registration_Middleware. The Renderable interface and Renderable_Middleware pair make it easy to register any hooks, shortcodes, post types, taxonomies, admin pages, and rest endpoints. Any class which needs to be processed, implements the Renderable interface and creates the ```function register(Hook_Hook_Loader $loader): void {...}


```php
class Some_Controller implements Hookable {
	public function register(Hook_Loader $loader): void{
		$loader->admin_action('some_action', [$this, 'some_action']);
	}
	public function some_action($some_arg): void {...}
}
```

Now when the init hook is called (priority 1), the some_action hook will be added. So long as the request comes from wp-admin. 

> For more details on Hookable and the Hook_Loader please see the full docs

### Registration Middleware ###

Custom registration processes can be added using Registration_Middleware. You can easily create your own middleware that implements the ```PinkCrab\Perique\Interfaces\Registration_Middleware ``` interface. This interface consists of a single method ``` process(object $class): void ``` which is available to each class.

```php
<?php

class Does_Something implements PinkCrab\Perique\Interfaces\Registration_Middleware {

	/** @var Some_Service */
	protected $some_service;
	
	public function __construct(Some_Service $some_service){
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

As of version 1.0.3 all middleware classes have access to the App's ```Hook_Loader``` and internal ```DI_Container```. These can be accessed using the following methods. This removes the need to create a custom ```Hook_Loader``` for each middleware and have access to the ```DI_Container``` without the need of Static Helpers

```php
class Does_Something implements PinkCrab\Perique\Interfaces\Registration_Middleware {
	
	/** @var Hook_Loader */
	protected $loader;

	/**	
	 * The hook loader is passed in, if this method is implemented.
	 */
	public function set_hook_loader(Hook_Loader $loader): void{
		$this->loader = $loader;
	}

	/** @var DI_Container */
	protected $container;

	/**	
	 * The container is passed in, if this method is implemented.
	 */
	public function set_di_container(DI_Container $container): void{
		$this->container = $container;
	}
}

```
> Due to when all of this is loaded, all Dependency rules might not be defined!

***

> The objects are passed fully constructed using the DI_Container

You can then pass these custom Registration_Middlewares to the app at boot.

```php
<?php 
// As an instance.
$app = ( new PinkCrab\Perique\Application\App_Factory )->with_wp_dice( true )
	// Rest of bootstrapping
	->registration_middleware(new Does_Something(new Some_Service()))
	->boot();
```

> Based on the complexity of your Middleware, you can either pass instances of the class's name.

```php
<?php 
// As a class name.
$app = ( new PinkCrab\Perique\Application\App_Factory )->with_wp_dice( true )
	// Rest of bootstrapping
	->construct_registration_middleware( Does_Something::class )
	->boot();
```

> These can either be passed before the app is booted, or afterwards.
> 
## Static Helpers ##

The App object has a few helper methods which can be called statically (either from an instance or from its name). 

### App::make(string $class, array $args = array()): object ###

* @param string $class Fully namespaced class name
* @param array<string, mixed> $args Constructor params if needed
* @return object Object instance
* @throws App_Initialization_Exception Code 4 If app isn't initialised.

` `  ` make() `  ` ` can be used to access the DI Container to fully resolve the dependencies of an object. 

```php 
$emailer = App::make(Customer_Emailer::class); 
$emailer->mail(ADMIN_EMAIL, 'Some Report', $email_body); 
$emailer->send(); 

```

### App::config(string $key, ...$child): mixed ###

* @param string $key The config key to call
* @param ...string $child Additional params passed.
* @return mixed
* @throws App_Initialization_Exception Code 4 If app isn't initialised.

Once the app has been booted you can access the App_Config values by either passing App_Config as a dependency or by using the Apps helper.

```php

// Get post type slug
$args = ['post_type' => App::config('post_types', 'my_cpt')];

// Get current plugin version.
$version = App::config('version');
```

> For more details on App_Config and its various use cases, [please checkout the full docs](https://app.gitbook.com/@glynn-quelch/s/pinkcrab/application/app_config).

### App::view(): View ###

* @return View
* @throws App_Initialization_Exception Code 4

If you need to render or return a template, you can use the ` `  ` view() `  ` ` helper. Returns an instance of the View class, populated with the current defined engine (use PHP by default).

```php
App::view()->render('signup/form', ['user' => wp_get_current_user(), 'nonce' => $nonce]);
```

> While the View and Config helpers are useful at times, its always better to inject them (App_Config::class or View::class).

## Hooks ##

We have a number of hooks you can use to extend or modify how the app works. All of our internal hooks have pinkcrab/pf/app/ prefix, but we have a class of constants you can use ```PinkCrab\Perique\Application\Hooks:: APP_INIT_*```


### Hooks::APP_INIT_PRE_BOOT ###

This is primarily used internally to make last minute changes to how the boot process works. Due to the way this hook is used (called when plugin.php is loaded) it should not be used from outside of your own code, as you can be 100% external code will load first.

```php
<?php
add_action( 
	Hooks::APP_INIT_PRE_BOOT, 
	function( App_Config $app_config, Hook_Loader $loader, DI_Container $container ): void {
		// do something cool
	}
);
```

### Hooks:: APP_INIT_PRE_REGISTRATION ###

During the boot processes, all classes passed for registration are processed on init hook, priority 1. The APP_INIT_PRE_REGISTRATION hook fires right before these are added. This allow you to hook in extra functionality to the application. This allows for extending your plugin with other plugins.

```php
<?php
add_action( 
	Hooks::APP_INIT_PRE_REGISTRATION, 
	function( App_Config $app_config, Hook_Loader $loader, DI_Container $container ): void {
		$some_controller = $container->create(Some_Other\Namespace\Some_Controller::class);
		$some_controller->load_hooks($loader);
	}
);
```

### Hooks:: APP_INIT_POST_REGISTRATION ###

After all the registration process has completed, this hook is fired. This allows you to check all has loaded correctly or if anything is missing. You can then fire notifications or disable functionality based on its results. *The internal loader is fired after this, so you can still use later hooks before initialisation.*

```php
<?php
add_action( 
	Hooks::APP_INIT_POST_REGISTRATION, 
	function( App_Config $app_config, Hook_Loader $loader, DI_Container $container ): void {
		if( ! has_action('some_action') ){
			// Do something due to action not being added.
		}
	}
);
```

### Hooks:: APP_INIT_CONFIG_VALUES ###

When the App_Config class is constructed with all values passed from ``` config/settings.php ``` this filter is fired during the initial boot process and should only really be used for internal purposes. Sadly due to the timing in which we use this filter, its not really suited for extending the plugin.

```php
<?php
add_filter(Hooks::APP_INIT_CONFIG_VALUES, 
	function( array $config ): array {
		$config['additional']['some_key'] = 'some value';
		return $config;
	}
);
```

### Hooks:: APP_INIT_REGISTRATION_CLASS_LIST ###

Filters all classes passed to the Registration Service before they are processed. This allows for hooking in from other plugins.

```php
<?php
add_filter(Hooks::APP_INIT_REGISTRATION_CLASS_LIST, 
	function( array $class_list ): array {
		$class_list[] = 'My\Other\Plugin\Service';
		$class_list[] = Another_Service::class;
		return $class_list;
	}
);
```

### Hooks:: APP_INIT_SET_DI_RULES ###

When the DI rules are set to the container, this filter is applied to all definitions. This allows for hooking in from external plugins and code to make use of the DI_Container. This combined with the other hooks allows for full expansion of your plugin.

```php
<?php
add_filter(Hooks::APP_INIT_SET_DI_RULES, 
	function( array $di_rules ): array {
		$di_rules['*'][Some_Interface::class] = Some_Class_Implementation::class;
		return $di_rules;
	}
);
```

## License ##

### MIT License ###

http://www.opensource.org/licenses/mit-license.html  

## Change Log ##
* 1.0.8 - Fixed incorrect interface used for Inject_App_Config in default DI rules added when App is initialised. Dev Dependencies updated to use current PinkCrab defaults. Removed .vscode config files from repo
* 1.0.7 - WPDB is now defined in the DI rules when the app is finailised (via `$app->boot()` method). Before it was only loaded if created using the App_Factory and not available before `init` is called.
* 1.0.6 - Added interfaces for injecting DI_Container, Hook_Loader and App_Config via a method with the help of an interface.
* 1.0.5 - When exporting the App_Config data, the array of meta data keys is now exported correctly. Also removed a declaration in View that was not needed.
* 1.0.4 - Added fallback values to url(), path(), additional() and namespaces() in App_Config() 
* 1.0.3 - DI Container and Hook Loader are now auto populated to Middleware if the ```public function set_hook_loader(Hook_Loader $loader):void{}``` and ```public function set_di_container(DI_Container $container):void{}``` methods are defined in the Middleware class.
* 1.0.2 - Ensure that middleware class names are only constructed during finalise when all internal DI rules are defined (esc DI Container instance.)
* 1.0.1 - Allow all middleware to be passed as classname and then constructed via the container, as well as allowing fully instantiated classes to be passed.
* 1.0.0 - Renamed Registerable interface to Hookable, including the internal Registerable_Middleware to Hookable_Middleware. Corrected typos, App::registration_classses() now App::registration_classes(), App_Initialization_Exception::registration_exists() to App_Initialization_Exception::registration_exists().
* 0.5.6 - Ensure App_Config is not populated as its DI ruleset as part of App->boot(). This ensures Config Facade is populated with the pass App_Config config array.
* 0.5.5 - Allows passing of registration middleware via App_Factory setup chain. Also allows the passing of DI_Container as a dependency via DI_Container. Allowing for modules to access DI without having to pass App and then use App::make(). 
* 0.5.4 - Moved to new repo to allow renaming via Packagist
* 0.5.3 - Move to new composer name due to issue with existing.
* 0.5.2 - Updated the primary namespace from PinkCrab\Perique to PinkCrab\Perique. Corrected typo on package name in composer.json, from perqiue to perique.
* 0.5.1 - Removed last Loader::class type hints and references. All now working with Hook_Loader::class
* 0.5.0  
  + Moved to the new Hook_Loader type. 
  + Updates to App_Config (creation of meta sub section and move to simple key/value pairs for post type and taxonomies).
  + Added setup() and tear_down() methods to the Registration_Middleware interface. 
  + Moved Collection into its [own repository](https://github.com/Pink-Crab/Collection). 
  + Removed unused service container interface.
* 0.4.1 - Updated tests to reflect the new Hook_Loader's internal structure (accessing protected state for tests)
* 0.4.0 - Introduced new app, with app factory to help with cleaner initialisation. Reintroduced Registration_Middleware which was removed in 0.2.0. Moved the registerables into a default piece of middleware which is automatically added at boot. Added a series of actions around the init callback which runs the registration process.
* 0.3.9 - Moved Loader into its own library, all tests and use statements updated.
* 0.3.8 - Added in missing Hook_Removal & Loader tests.
* 0.3.7 - Added in Hook_Removal and made minor changes to the Loader tests.
* 0.3.6 - Added remove_action() and remove_filter() to Loader
* 0.3.5 - Added coverage reports to gitignore
* 0.3.4 - Improved tests and hooked to codecov
* 0.3.3 - Removed object type hint from service container.
* 0.3.2 - Added in tests and expanded view
* 0.3.1 - Minor docblock changes for phpstan lv8
