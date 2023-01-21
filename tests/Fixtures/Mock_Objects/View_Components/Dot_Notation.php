<?php

declare(strict_types=1);

/**
 * Dot Notation Component model for templating.
 *
 * Access the path using dot notation.
 *
 * @since 1.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Perique\Tests\Fixtures\Mock_Objects\View_Components;

use PinkCrab\Perique\Services\View\Component\Component;

class Dot_Notation extends Component {

	public $variable;

	// constructor
	public function __construct( string $variable ) {
		$this->variable = $variable;
	}

    /**
	 * Template method for the component.
	 *
	 * @return string
	 */
	public function template(): string {
		return 'other.dot-notation';
	}
}
