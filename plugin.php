<?php


/**
 * Plugin Name: Core Test Bed
 */

use PinkCrab\Core\Interfaces\Renderable;
use PinkCrab\Core\Application\App_Factory;
use PinkCrab\Core\Interfaces\DI_Container;
use PinkCrab\Core\Services\View\PHP_Engine;

 require_once __DIR__ . '/vendor/autoload.php';

$app = App_Factory::with_wp_di(
	array(
		'*' => array(
			'substitutions' => array(
				Renderable::class => new PHP_Engine( __DIR__ ),
				wpdb::class       => $GLOBALS['wpdb'],
			),
		),
	)
);

$app->app_config( array() );
$app->registration_classses( array() );
$app->container_config( fn( DI_Container $container ) => $container->addRules( array() ) );
$app->boot();
 dump( $app );
 dump( $app::view() );
