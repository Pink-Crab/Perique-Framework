<?php

declare(strict_types=1);
/**
 * Main App Container Test.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\Mock_Objects;

class Sample_Class {

	/**
	 * Test property
	 *
	 * @var string
	 */
	public $property_a = 'Alpha';

	/**
	 * Get the value of property_a
	 */
	public function get_property_a(): string {
		return $this->property_a;
	}

	/**
	 * Set the value of property_a
	 *
	 * @return self
	 */
	public function set_property_a( string $property_a ): self {
		$this->property_a = $property_a;
		return $this;
	}
}
