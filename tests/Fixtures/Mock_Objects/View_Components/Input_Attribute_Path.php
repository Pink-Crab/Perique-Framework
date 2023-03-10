<?php

declare(strict_types=1);

/**
 * Input Component model for templating.
 *
 * Assumes path based on filename, regardless of case.
 *
 * @since 1.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components;

use PinkCrab\Perique\Services\View\Component\Component;

/**
 * @view from/attribute/path
 * @since 1.0.0
 */
class Input_Attribute_Path extends Component {

	public $name;

	public function __construct( string $name ) {
		$this->name = $name;
	}
}
