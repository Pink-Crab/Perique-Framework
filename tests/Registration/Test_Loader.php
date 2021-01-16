<?php

declare(strict_types=1);

/**
 * Loader tests.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Registration;

use WP_UnitTestCase;
use PinkCrab\Core\Services\Registration\Loader;


class Loader_Test extends WP_UnitTestCase {

	/**
	 * Loader
	 *
	 * @var \PinkCrab\Core\Services\Registration\Loader
	 */
	public $loader;

	/**
	 * Setup tests with an instance of the loader.
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->loader = Loader::boot();
	}

	/**
	 * Registers the hooks.
	 *
	 * @return void
	 */
	private function register_hooks() {
		$this->loader->register_hooks();
	}

	/**
	 * Test that the loader is infact the loader.
	 *
	 * @return void
	 */
	public function test_is_loader_loaded() {
		$this->assertInstanceOf(
			Loader::class,
			$this->loader
		);
	}

	/**
	 * Test that actions can be added.
	 *
	 * @return void
	 */
	public function test_action_can_be_added_to_loader() {

		// Test Global Action
		$this->loader->action(
			'test_action',
			function() {
				return 3;
			}
		);

		$this->loader->register_hooks();

		$this->assertTrue( has_action( 'test_action' ) );
	}

	/**
	 * Test that filters are added and used.
	 *
	 * @return void
	 */
	public function test_that_filters_can_be_added() {
		$this->loader->filter(
			'test_filter',
			function( $inital ) {
				return 'replaced';
			}
		);

		$this->register_hooks();
		$this->assertEquals( 'replaced', apply_filters( 'test_filter', 'initial' ) );
	}

	/**
	 * Test that ajax calls can be added to the loader.
	 *
	 * @return void
	 */
	public function test_ajax_calls_can_be_added_to_loader(): void {

		// Check defaults are used for both public & private
		$this->loader->ajax(
			'ajax_test_all',
			function() {
				return true;
			}
		);

		// Test only adding to public
		$this->loader->ajax(
			'ajax_test_public',
			function() {
				return true;
			},
			true,
			false
		);

		// Test only adding to private.
		$this->loader->ajax(
			'ajax_test_private',
			function() {
				return true;
			},
			false,
			true
		);

		// Register all hooks.
		$this->register_hooks();

		// Test the public & private.
		$this->assertTrue( has_action( 'wp_ajax_ajax_test_all' ) );
		$this->assertTrue( has_action( 'wp_ajax_nopriv_ajax_test_all' ) );

		// Test public
		$this->assertFalse( has_action( 'wp_ajax_ajax_test_public' ) );
		$this->assertTrue( has_action( 'wp_ajax_nopriv_ajax_test_public' ) );

		// Test private
		$this->assertTrue( has_action( 'wp_ajax_ajax_test_private' ) );
		$this->assertFalse( has_action( 'wp_ajax_nopriv_ajax_test_private' ) );
	}

	/**
	 * Check that only front or admin hooks can be called when in admin/public
	 *
	 * @return void
	 */
	public function test_admin_hooks_are_not_called_in_public(): void {

		// Filters
		$this->loader->admin_filter(
			'test_admin_filter',
			function( $inital ) {
				return 'replaced_with_admin_filter';
			}
		);
		$this->loader->front_filter(
			'test_front_filter',
			function( $inital ) {
				return 'replaced_with_front_filter';
			}
		);

		// Actions
		$this->loader->admin_action(
			'test_admin_action',
			function() {
				return true;
			}
		);
		$this->loader->front_action(
			'test_front_action',
			function() {
				return true;
			}
		);

		// Register all hooks.
		$this->register_hooks();

		$this->assertEquals( 'initial', apply_filters( 'test_admin_filter', 'initial' ) );
		$this->assertEquals( 'replaced_with_front_filter', apply_filters( 'test_front_filter', 'initial' ) );

		$this->assertFalse( has_action( 'test_admin_action' ) );
		$this->assertTrue( has_action( 'test_front_action' ) );

	}

	/**
	 * Check that shortcodes can be added and called.
	 *
	 * @return void
	 */
	public function test_shortcodes_can_be_added(): void {
		$this->loader->shortcode(
			'testShortCode',
			function( $atts ) {
				echo $atts['text'];
			}
		);

		$this->register_hooks();

		// Check the shortcode returns yes.
		ob_start();
		do_shortcode( "[testShortCode text='yes']" );
		$this->assertTrue( ob_get_contents() === 'yes' );
		ob_end_clean();
	}
}