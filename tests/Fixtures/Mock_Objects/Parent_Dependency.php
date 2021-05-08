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

use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Sample_Class;
use PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Interface_Get;

class Parent_Dependency implements Interface_Get {

	/**
	 * Test property
	 *
	 * @var \PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Sample_Class
	 */
	public $sample_class;

	public function __construct( Sample_Class $sample_class ) {
		$this->sample_class = $sample_class;
	}

	/**
	 * Returns its nested dependecny.
	 *
	 * @return \PinkCrab\Perique\Tests\Fixtures\Mock_Objects\Sample_Class
	 */
	public function get_sample_class(): Sample_Class {
		return $this->sample_class;
	}

	/**
	 * Get the value of property_a
	 */
	public function get_property_a(): string {
		return $this->sample_class->property_a;
	}

	/**
	 * Set the value of property_a
	 *
	 * @return self
	 */
	public function set_property_a( string $property_a ): self {
		$this->sample_class->property_a = $property_a;
		return $this;
	}
}
