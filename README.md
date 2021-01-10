# PinkCrab Framework Core #

Welcome the main package of the PinkCrab Framwework. 

## Setup ##

To use the Framework, a few files are needed for the framework to be loaded.
*bootstrap.php*
This file can be anywhere in your plugin, although we reccomend keeping it in your root directory with the plugin.php file.

````php

    // Namespaces
    use PinkCrab\Core\Application\App;
    use PinkCrab\Core\Services\Dice\Dice;
    use PinkCrab\Core\Collection\Collection;
    use PinkCrab\Core\Services\Dice\WP_Dice;
    use PinkCrab\Core\Application\App_Config;
    use PinkCrab\Core\Services\Registration\Loader;
    use PinkCrab\Core\Services\ServiceContainer\Container;
    use PinkCrab\Core\Services\Registration\Register_Loader;

    $loader    = Loader::boot();
    $config    = new App_Config( require( 'config/settings.php' ) ); // Change if using custom path for config.
    $container = new Container();

    // Setup the service container .
    $container->set( 'di', WP_Dice::constructWith( new Dice() ) );
    $container->set( 'config', $config );

    // Boot the app.
    $app = App::init( $container );
    // Add all DI rules and register the actions from loader.
    add_action(
        'init',
        function () use ( $loader, $app, $config ) {
            dump( $loader, $app, $config );
            // Add all DI rules.
            $app->get( 'di' )->addRules(
                apply_filters( 'PinkCrab\\di_rules', require( 'config/dependencies.php' ) ) // Change if using custom path for config.
            );
            // Initalise all registerable classes.
            Register_Loader::initalise(
                $app,
                apply_filters( 'PinkCrab\\registration_rules', require( 'config/registration.php' ) ), // Change if using custom path for config.
                $loader
            );

            // Register Loader hooks.
            $loader->register_hooks();
        },
        1
    );

````
If you are planning to give all of your vendor libraries custom namespaces using Php Scoper (more details below), to use the new mapped namespaces.

Once you have your bootstrap file created, its just a case of hooking it up in your plugin.php file.

````php
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



    if ( ! defined( 'ABSPATH' ) ) {
        die;
    }

    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/bootstrap.php';

    // Optional activation hooks
````