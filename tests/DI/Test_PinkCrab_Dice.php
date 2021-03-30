<?php

declare(strict_types=1);

/**
 * Tests for the WP_Dice wrapper.
 *
 * @since 0.3.1
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Tests\DI;

use DateTime;
use stdClass;
use Dice\Dice;
use WP_UnitTestCase;
use ReflectionException;
use PinkCrab\Core\Tests\Fixtures\DI\Class_F;
use PinkCrab\Core\Tests\Fixtures\DI\Class_G;
use PinkCrab\Core\Tests\Fixtures\DI\Class_H;
use PinkCrab\Core\Services\Dice\PinkCrab_Dice;
use PinkCrab\Core\Tests\Fixtures\DI\Abstract_B;
use PinkCrab\Core\Tests\Fixtures\DI\Interface_A;
use PinkCrab\Core\Tests\Fixtures\DI\Dependency_C;
use PinkCrab\Core\Tests\Fixtures\DI\Dependency_D;
use PinkCrab\Core\Tests\Fixtures\DI\Dependency_E;
use PinkCrab\Core\Exceptions\DI_Container_Exception;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Sample_Class;
use Gin0115\WPUnit_Helpers\Objects as WPUnit_HelpersObjects;

class Test_PinkCrab_Dice extends WP_UnitTestCase {

	/**
	 * Rules
	 *
	 * Any class which implements Interface_A will use Depenedcy_D
	 * Any class which extends Abstract_B will use Dependency_C
	 * Class_H will use Dependency_E to implement Interface_A
	 *
	 * @var array
	 */
	protected $dice_rules = array(
		Interface_A::class => array(
			'instanceOf' => Dependency_D::class,
		),
		Abstract_B::class  => array(
			'instanceOf' => Dependency_C::class,
		),
		Class_H::class     => array(
			'substitutions' => array(
				Interface_A::class => Dependency_E::class,
			),
		),
	);

	/** @testdox It should be possible to use the container in a purely fluent without using NEW */
	public function test_constuctwith_factory(): void {
		$wp_dice = PinkCrab_Dice::withDice( new Dice() );
		$this->assertInstanceOf( PinkCrab_Dice::class, $wp_dice );

		// Check hold instance of Dice.
		$this->assertInstanceOf(
			Dice::class,
			WPUnit_HelpersObjects::get_property( $wp_dice, 'dice' )
		);
	}

	/** @testdox Objects with with classes and interfaces as dependencies should be resolved if all rules that can not be determined by type, are supplied as rules. */
	public function test_can_populate_with_rules(): void {
		$wp_dice = PinkCrab_Dice::withDice( new Dice() );
		$wp_dice->addRules( $this->dice_rules );

		$this->assertInstanceOf( Class_H::class, $wp_dice->create( Class_H::class ) );
		$this->assertInstanceOf( Class_G::class, $wp_dice->create( Class_G::class ) );
		$this->assertInstanceOf( Class_F::class, $wp_dice->create( Class_F::class ) );

		$this->assertEquals(
			Dependency_E::class,
			$wp_dice->create( Class_H::class )->test()
		);
		$this->assertEquals(
			Dependency_D::class,
			$wp_dice->create( Class_G::class )->test()
		);
		$this->assertEquals(
			Dependency_C::class,
			$wp_dice->create( Class_F::class )->test()
		);

	}

	/** @testdox If attemepting to create a class that doesnt exist and error should be generated and the system abort. */
	public function test_exception_thrown_if_none_existing_class(): void {
		$this->expectException( ReflectionException::class );
		$wp_dice = PinkCrab_Dice::withDice( new Dice() );
		$wp_dice->create( 'NotAClass' );
	}

	/** @testdox It should be possible to add single DI rule to the container */
	public function test_test_can_add_rule(): void {
		$wp_dice = PinkCrab_Dice::withDice( new Dice() );
		$result  = $wp_dice->addRule( stdClass::class, array( 'instanceOf' => DateTime::class ) );
		$this->assertInstanceOf( DateTime::class, $result->create( stdClass::class ) );
	}

	/** @testdox It should be possible to check if a class either has a rule defined or exists as a valid class*/
	public function test_has(): void {
		$wp_dice = PinkCrab_Dice::withDice( new Dice() );

		// As a global
		$wp_dice->addRule(
			'*',
			array(
				'substitutions' => array(
					Interface_A::class => Dependency_E::class,
				),
			)
		);
		$this->assertTrue( $wp_dice->has( Interface_A::class ) );

		// As a single rule
		$wp_dice->addRule(
			Abstract_B::class,
			array(
				'instanceOf' => Dependency_C::class,
			)
		);
		$this->assertTrue( $wp_dice->has( Abstract_B::class ) );

		// General object.
		$this->assertTrue( $wp_dice->has( Sample_Class::class ) );
	}

	/** @testdox It should be possible to create objects using only the rules defined and without the option of passing params. It should also be PSR complient. */
	public function test_can_create_purely_using_autowiring(): void {
		$wp_dice = PinkCrab_Dice::withDice( new Dice() );
		$this->assertInstanceOf( Sample_Class::class, $wp_dice->get( Sample_Class::class ) );
	}

	/** @testdox If attempeting to use the pure autowire on a class that doest exist and error should be generated and the systm aborted */
	public function test_throws_exception_using_undefined_class_on_get(): void {
		$this->expectException( DI_Container_Exception::class );
		$wp_dice = PinkCrab_Dice::withDice( new Dice() );
		$wp_dice->get( 'NotAClass' );
	}

}
