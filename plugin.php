<?php


/** 
 * Plugin Name: Core Test Bed
 */

use PinkCrab\Core\Application\App_Factory;
use PinkCrab\Core\Interfaces\Renderable;
use PinkCrab\Core\Services\View\PHP_Engine;

 require_once __DIR__ . '/vendor/autoload.php';

 $app = App_Factory::with_wp_di(
    array(
        // Gloabl Rules
        '*' => array(
            'substitutions' => array(
                Renderable::class => PHP_Engine::class,
                wpdb::class       => $GLOBALS['wpdb'],
            ),
        ),
        PHP_Engine::class => [
            'constructParams' => array( __DIR__ ),
        ]
        /** ADD YOUR CUSTOM RULES HERE */
    )
 );
 $app->app_config([]);
 $app->registration_classses([]);
 dump($app);
 dump($app::view());