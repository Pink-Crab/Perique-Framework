---
description: >-
  The core components of the PinkCrab framework, a small and highly extendable
  framework for building WordPress Plugins and Themes.
---

# PinkCrab Framework::Core

The PinkCrab Framework 

### Setup.

You can clone our existing **Plugin Boiler Plate** on [github](https://github.com/Pink-Crab/Framework_Plugin_Boilerplate), if however, you wanted to construct the application you can. The framework can work as a standalone plugin, or setup slightly differently to be used a core library in the mu-plugins directory or driving a more MVC orientated theme.

The framework uses composer, normally this is not advised for WordPress plugins/themes. We have a small config for using [php-scoper](https://github.com/humbug/php-scoper/) to move the entire vendor directory to a prefixed namespace. More details can be found at the end of this page.

{% hint style="info" %}
The rest of this tutorial assumes you have composer installed globally.
{% endhint %}

### Structure

```text
+ plugin
    + config
        | dependencies.php
        | registration.php
        | settings.php
    + src
    | bootstrap.php
    | composer.json
    | plugin.php


// OPTIONAL (Comes included in boilerplate repo)
    + views
    + assets
    + wp
        | Activation.php
        | Deactivation.php
        | Uninstalled.php
    + tests
        | bootsrap.php
        | wp_config.php
    | phpcs.xml
    | phpstan.neon.dist
    | phpunit.xml.dist
```

Create your directory and initialise a composer project with `composer init`from the command line. To add the Framework type `composer require pinkcrab/plugin-framework` to add to your plugin. You can add any additional libraries using **packagist** as you would any other PHP project. Then it's just a case of filling the rest of your composer.json file \([see an example](https://github.com/Pink-Crab/Framework_Plugin_Boilerplate/blob/master/composer.json)\)

Once you have all of your composer.json setup, you can run composer install, and then it's a case of hooking your application up to WordPress. 

#### Config

At its bare minimum, we have 3 files that hold most of our global configs, registrations, and dependency injection wiring. These can be placed anywhere within your application and are just basic PHP files just return arrays \(so no need to be included in the autoloader\).

```php
// @file config/dependencies.php

<?php

declare(strict_types=1);

/**
 * Handles all depenedency injection rules and config.
 *
 * @package PinkCrab\PluginBoilerplate
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

use PinkCrab\Core\Application\App;
use PinkCrab\Core\Interfaces\Renderable;
use PinkCrab\Core\Services\View\PHP_Engine;

return array(
	// Gloabl Rules
	'*'         => array(
		'substitutions' => array(
			App::class        => App::get_instance(),
			Renderable::class => PHP_Engine::class,
		),
	),

	// Use wpdb as an injectable object.
	wpdb::class => array(
		'shared'          => true,
		'constructParams' => array( \DB_USER, \DB_PASSWORD, \DB_NAME, \DB_HOST ),
	),

	/** ADD YOUR CUSTOM RULES HERE */
);
```

The Dependencies file holds all our Dependency Injection rules, our boilerplate plugin 

