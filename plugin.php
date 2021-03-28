<?php


/**
 * Plugin Name: Core Test Bed
 */

use PinkCrab\Core\Interfaces\Renderable;
use PinkCrab\Core\Application\App_Factory;
use PinkCrab\Core\Services\View\PHP_Engine;

//  require_once __DIR__ . '/vendor/autoload.php';

$app = ( new App_Factory() )->with_wp_di( true );

// $app->set_app_config( array() );
// $app->registration_classses( array() );
// $app->container_config(
// 	function( $container ) {
// 		dump( $container );
// 	}
// );
//  dump( $app );
//  dump( $app::view() );
