<?php

declare(strict_types=1);

/**
 * Input Component model for templating.
 *
 * Gets templated using the template method.
 *
 * @since 1.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components;

use PinkCrab\Perique\Services\View\Component\Component;

class Input_Template_Method extends Component {

	public $name;

	// constructor
	public function __construct( string $name ) {
		$this->name = $name;
	}

	/**
	 * Template method for the component.
	 *
	 * @return string
	 */
	public function template(): string {
		return 'path/to/template';
	}
}
