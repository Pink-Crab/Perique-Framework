<?php

declare(strict_types=1);
/**
 * Dependency E
 * Implements Interface_A
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\DI;

use PinkCrab\Perique\Interfaces\DI_Container;


class Has_DI_Dependency {

	/** @var DI_Container */
	public $di;

	public function __construct( DI_Container $di ) {
		$this->di = $di;
	}

}
