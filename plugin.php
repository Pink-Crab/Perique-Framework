<?php

use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Interfaces\Hookable;
use PinkCrab\Perique\Application\App_Factory;
use PinkCrab\Perique\Migration\Migration\Migrations;
use PinkCrab\Plugin_Lifecycle\Plugin_State_Controller;
use PinkCrab\Perique\Migration\Tests\Fixtures\Migration\Simple_Table_Migration;
use PinkCrab\Perique\Migration\Tests\Fixtures\LifeCycle\Activation_Write_Option;

/**
 * Plugin Name: AA Perique V2
 * Plugin URI: https://wpspecialprojects.wordpress.com/
 * Description: dffdsfsdfsd
 * Version: 0.1.0
 * Author: WordPress.com Special Projects
 * Author URI: https://wpspecialprojects.wordpress.com/
 * Text Domain: team51-paypal-donations
 * Domain Path: /languages
 * Tested up to: 5.8
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 **/

require_once __DIR__ . '/vendor/autoload.php';

// class Foo implements Hookable {
// 	public function register( Hook_Loader $loader ): void {
// 		$loader->action(
// 			'wp_head',
// 			function() {
// 				die( 'ggggg' );
// 			}
// 		);
// 	}
// }

// // Setup and Boot Perique as normal
// $app = ( new App_Factory( __DIR__ . '/data' ) )
// 	->default_setup()
// 	->registration_classes( array( Foo::class ) )
// 	->boot();

// add_action(
// 	'init',
// 	function() use ( $app ) {
// 		// dump( $app );
// 	}
// );
