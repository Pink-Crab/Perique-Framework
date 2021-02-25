<?php

declare(strict_types=1);

/**
 * Hook_Removal tests.
 *
 * @since 0.3.6
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\Registration;

use WP_UnitTestCase;
use PinkCrab\Core\Services\Registration\Hook_Removal;
use PinkCrab\Core\Tests\Fixtures\Loader\Hooks_Via_Static;
use PinkCrab\Core\Tests\Fixtures\Loader\Hooks_Via_Instance;

class Test_Hook_Removal extends WP_UnitTestCase {

	/**
	 * Static Action
	 */
	public function test_can_remove_static_action() {
		// Register action.
		( new Hooks_Via_Static() )->register_action();

		$response = ( new Hook_Removal(
			Hooks_Via_Static::ACTION_HANDLE,
			array( Hooks_Via_Static::class, 'action_callback_static' )
		) )->remove();

		$this->assertTrue( $response );
		$this->assertEmpty( $GLOBALS['wp_filter'][ Hooks_Via_Static::ACTION_HANDLE ]->callbacks[10] );
	}

	/**
	 * Static Filter
	 */
	public function test_can_remove_static_filter() {
		// Register action.
		( new Hooks_Via_Static() )->register_filter();

		$response = ( new Hook_Removal(
			Hooks_Via_Static::FILTER_HANDLE,
			array( Hooks_Via_Static::class, 'filter_callback_static' )
		) )->remove();

		$this->assertTrue( $response );
		$this->assertEmpty( $GLOBALS['wp_filter'][ Hooks_Via_Static::FILTER_HANDLE ]->callbacks[10] );
	}

	/**
	 * Instanced Action
	 */
	public function test_can_remove_instanced_action() {
		// Register action.
		( new Hooks_Via_Instance() )->register_action();

		$response = ( new Hook_Removal(
			Hooks_Via_Instance::ACTION_HANDLE,
			array( new Hooks_Via_Instance(), 'action_callback_instance' )
		) )->remove();

		$this->assertTrue( $response );
		$this->assertEmpty( $GLOBALS['wp_filter'][ Hooks_Via_Instance::ACTION_HANDLE ]->callbacks[10] );
	}

	public function test_can_remove_instanced_filter() {
		// Register action.
		( new Hooks_Via_Instance() )->register_filter();

		$response = ( new Hook_Removal(
			Hooks_Via_Instance::FILTER_HANDLE,
			array( new Hooks_Via_Instance(), 'filter_callback_instance' )
		) )->remove();

		$this->assertTrue( $response );
		$this->assertEmpty( $GLOBALS['wp_filter'][ Hooks_Via_Instance::FILTER_HANDLE ]->callbacks[10] );
	}

	public function test_can_remove_global_functon() {
        add_action( 'test_global_function', 'pc_tests_noop' );
		$response = ( new Hook_Removal( 'test_global_function', 'pc_tests_noop' ) )
            ->remove();

        $this->assertTrue( $response );
		$this->assertEmpty( $GLOBALS['wp_filter'][ 'test_global_function' ]->callbacks[10] );
	}

    public function test_returns_false_for_closures(): void
    {
        add_action(
			'clousre_hook',
			function() {
				echo 'THIS CAN NOT BE REMOVED';
			}
		);
		$this->assertFalse(
			( new Hook_Removal(
				'clousre_hook',
				function() {
					echo 'THIS CAN NOT BE REMOVED';
				}
			) )->remove()
		);
    }
}
