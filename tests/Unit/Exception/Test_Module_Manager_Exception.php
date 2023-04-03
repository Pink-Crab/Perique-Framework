<?php

declare(strict_types=1);

/**
 * Unit tests for the Module Manager Exceptions.
 *
 * @since 2.0.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Unit\View;

use PinkCrab\Perique\Exceptions\Module_Manager_Exception;

/**
 * @group exception
 * @group unit
 */
class Test_Module_Manager_Exception extends \WP_UnitTestCase {

	/**
	 * Mixed data type provider.
	 *
	 * @return array<int, array{0:mixed, 1:string}>
	 */
	public function mixed_data_provider(): array {
		return array(
			'object'     => array( new \stdClass(), 'stdClass' ),
			'int'        => array( 1, '1' ),
			'float'      => array( 10.1, '10.1' ),
			'string'     => array( 'string', 'string' ),
			'bool_true'  => array( true, 'BOOL::true' ),
			'bool_false' => array( false, 'BOOL::false' ),
			'null'       => array( null, 'NULL' ),
			'array'      => array( array( 'Apple', 2, 3 ), '["Apple",2,3]' ),
		);
	}

	/** @testdox It should be possible to throw an exception for an invalid module class name with an error code of 20 */
	public function test_can_throw_invalid_module_class_name(): void {
		$this->expectException( Module_Manager_Exception::class );
		$this->expectExceptionCode( 20 );
		$this->expectExceptionMessage( 'some-none-class must be an instance of the Module interface' );

		throw Module_Manager_Exception::invalid_module_class_name( 'some-none-class' );
	}

	/**
	 * @testdox It should be possible to create an exception for when a Registration_Middleware can not be created with error code of 21
	 * @dataProvider mixed_data_provider
	 */
	public function test_can_throw_failed_to_create_registration_middleware( $created, $expected ): void {
		$this->expectException( Module_Manager_Exception::class );
		$this->expectExceptionCode( 21 );
		$this->expectExceptionMessage( "Failed to create Registration_Middleware, invalid instance created. Created: {$expected}" );

		throw Module_Manager_Exception::failed_to_create_registration_middleware( $created );
	}

	/**
	 * @testdox It should be possible to create an exception for when a class passed as Registration_Middleware does not implement the interface (when built via DI) with error code of 22
	 * @dataProvider mixed_data_provider
	 */
	public function test_can_throw_invalid_registration_middleware( $created, $expected ): void {
		$this->expectException( Module_Manager_Exception::class );
		$this->expectExceptionCode( 22 );
		$this->expectExceptionMessage( "{$expected} was returned as the modules Middleware, but this does not implement Registration_Middleware interface" );

		throw Module_Manager_Exception::invalid_registration_middleware( $created );
	}

	/**
	 * @testdox It should be possible to create an exception for when a none class-string is passed to the class list in registration service with error code of 23
	 * @dataProvider mixed_data_provider
	 */
	public function test_can_throw_invalid_class_list( $created, $expected ): void {
		$this->expectException( Module_Manager_Exception::class );
		$this->expectExceptionCode( 23 );
		$this->expectExceptionMessage( "None class-string \"{$expected}\" passed to the registration class list" );

		throw Module_Manager_Exception::none_class_string_passed_to_registration( $created );
	}
}
