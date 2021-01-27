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

use Dice\Dice;
use WP_UnitTestCase;
use ReflectionException;
use PinkCrab\PHPUnit_Helpers\Objects;
use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\Core\Tests\Fixtures\DI\Class_F;
use PinkCrab\Core\Tests\Fixtures\DI\Class_G;
use PinkCrab\Core\Tests\Fixtures\DI\Class_H;
use PinkCrab\Core\Tests\Fixtures\DI\Abstract_B;
use PinkCrab\Core\Tests\Fixtures\DI\Interface_A;
use PinkCrab\Core\Tests\Fixtures\DI\Dependency_C;
use PinkCrab\Core\Tests\Fixtures\DI\Dependency_D;
use PinkCrab\Core\Tests\Fixtures\DI\Dependency_E;

class Test_WP_Dice extends WP_UnitTestCase {

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

	/**
	 * Test
	 *
	 * @return void
	 */
	public function test_constuctwith_factory(): void {
		$wp_dice = WP_Dice::constructWith( new Dice() );
		$this->assertInstanceOf( WP_Dice::class, $wp_dice );

		// Check hold instance of Dice.
		$this->assertInstanceOf(
			Dice::class,
			Objects::get_private_property( $wp_dice, 'dice' )
		);
	}

	/**
	 * Test rules can be passed to DICE
	 *
	 * @return void
	 */
	public function test_can_populate_with_rules(): void {
		$wp_dice = WP_Dice::constructWith( new Dice() );
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

    /**
     * Test an exception is thrown if creating none existing class
     *
     * @return void
     */
	public function test_exception_thrown_if_none_existing_class(): void {
		$this->expectException( ReflectionException::class );
		$wp_dice = WP_Dice::constructWith( new Dice() );
		$wp_dice->create( 'NotAClass' );
	}

}
